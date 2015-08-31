<table>
<tbody>
  <tr>
    <td class="informations">
      <?php include_partial('show_informations', array('product' => $product)) ?>
      <?php include_partial('global/show_links', array('objects' => $product)) ?>
      <div class="clear"></div>
    </td>
    <td class="declinations">
      <?php foreach ( $declinations as $declination ): ?>
        <?php include_partial('show_declination', array('declination' => $declination)) ?>
      <?php endforeach ?>
      <div class="clear"></div>
      <form method="get" action="<?php echo url_for('cart/show') ?>" class="cart">
        <input type="submit" name="submit" value="<?php echo __('Cart') ?>" />
      </form>
    </td>
  </tr>
<body>
</table>
<?php use_javascript('pub-totals?'.date('Ymd')) ?>
