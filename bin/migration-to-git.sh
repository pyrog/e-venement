diff --git a/lib/model/doctrine/packages/email/PluginEmail.class.php b/lib/model/doctrine/packages/email/PluginEmail.class.php
index a0c61b5..ed4e002 100644
--- a/lib/model/doctrine/packages/email/PluginEmail.class.php
+++ b/lib/model/doctrine/packages/email/PluginEmail.class.php
@@ -17,6 +17,7 @@ abstract class PluginEmail extends BaseEmail
   public $to              = array();
   public $mailer          = NULL;
   public $from_txt        = NULL;
+  protected $embedded_images = 0;
   
   protected function isNewsletter()
   {
@@ -88,6 +89,7 @@ abstract class PluginEmail extends BaseEmail
     if ( $this->field_cc )
       $message->setCc($this->field_cc);
     
+    // attach normal file attachments
     foreach ( $this->Attachments as $attachment )
     {
       $id = $attachment->getId() ? $attachment->getId() : date('YmdHis').rand(10000,99999);
@@ -99,6 +101,10 @@ abstract class PluginEmail extends BaseEmail
       $message->attach($att);
     }
     
+    // force setting the Content-Type to 'multipart/related' to really follow the norm
+    if ( $this->embedded_images > 0 )
+      $message->setContentType('multipart/related');
+    
     $this->setMailer();
     
     return $immediatly === true
@@ -121,24 +127,51 @@ abstract class PluginEmail extends BaseEmail
 
   protected function compose(Swift_Message $message)
   {
+    $relatedPart = Swift_MimePart::newInstance(null, 'text/relative', null);
+    
+    // treat inline images
+    $post_treated_content = $this->content;
+    preg_match_all('!<img\s(.*)src="data:(image/\w+);base64,(.*)"(.*)/>!U', $post_treated_content, $imgs, PREG_SET_ORDER);
+    foreach ( $imgs as $i => $img )
+    {
+      $att = Swift_Attachment::newInstance()
+        ->setFileName("img-$i.".str_replace('image/', '', $img[2]))
+        ->setContentType($img[2])
+        ->setDisposition('inline')
+        ->setBody(base64_decode($img[3]))
+        ->setId("img$i.$i@e-venement")
+      ;
+      
+      // embedding the image
+      $post_treated_content = str_replace(
+        $img[0],
+        '<img '.$img[1].' '.$img[4].' src="'.$message->embed($att).'" />',
+        $post_treated_content
+      );
+      
+      $this->embedded_images++;
+    }
+    $post_treated_content .= '<img src="cid:part.47@e-venement" />';
+    
     $content = 
       '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
       '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">'.
       '<head>'.
-      //'<title></title>'.
       '<title>'.$this->field_subject.'</title>'.
       '<meta name="title" content="'.$this->field_subject.'" />'.
       '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.
       '</head><body>'.
-      $this->content.
+      $post_treated_content.
       '</body></html>';
     
     $h2t = new HtmlToText($content);
     $message
       ->setFrom(array($this->field_from => $this->from_txt ? $this->from_txt : $this->field_from))
       ->setSubject($this->field_subject)
-      ->setBody($h2t->get_html(),'text/html')
-      ->addPart($h2t->get_text(),'text/plain');
+      ->addPart($h2t->get_html(), 'text/html')
+      ->addPart($h2t->get_text(),'text/plain')
+    ;
+    
     if ( $this->read_receipt )
       $message->setReadReceiptTo($this->field_from);
     return $message;
