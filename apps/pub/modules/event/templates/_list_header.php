<?php include_partial('global/ariane',array('active' => 1)) ?>
<?php include_partial('global/flashes') ?>

<?php if ( $slug = $sf_request->getRawValue()->getParameter('meta-event', false) ): ?>
<?php $slugs = is_array($slug) ? $slug : array($slug) ?>
  <?php $mes = Doctrine::getTable('MetaEvent')->createQuery('me')->andWhereIn('me.slug', $slugs) ?>
  <?php foreach ( $mes as $me ): ?>
  <h2><?php echo $me ?></h2>
  <?php endforeach ?>
<?php endif ?>
