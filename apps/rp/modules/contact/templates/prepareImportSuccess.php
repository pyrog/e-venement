<?php include_partial('assets') ?>
<?php include_partial('global/flashes') ?>
<div class="ui-widget ui-widget-content ui-corner-all tdp-import">
  <div class="tdp-widget-header ui-widget-header ui-corner-all">
    <h1><?php echo __('Import') ?></h1>
  </div>
  <div class="tdp-explanation ui-widget-content ui-corner-all">
    <p><?php echo __('You are about to import a bulk of contacts. Be careful to respect this type of file:') ?></p>
    <pre>
ID,ETABLISSEMENT,CIVILITE,PRENOM,NOM,ADRESSE1,ADRESSE2,CP,VILLE,PAYS,GROUPE,TELEPHONE,EMAIL,LANGUAGE
1,DCAP,Mme,Marie-Claire,DURAND,MAIRIE D'ECHIROLLES,,38130,ECHIROLLES,FRANCE,ASSOCULT,0707070707,contact@ville-echirolles.fr
2,"CULTURES DU CŒUR",Mme,Jeanne,CHANCELLE,11 RUE DOCTEUR LAENNEC,,38610,GIERES,FRANCE,ASSOCULT,04 76 24 08 48,culturesducoeur@free.fr,en
3,"","M.",Christian,MAJEUR,9 ALLEE DES LAURIERS,,69300,CALUIRE,FRANCE,AUTEUR,,,en
    </pre>
    <p><?php echo __('Do not forget that this file must be UTF-8 encoded, its fields must be separated by a comma, and the first line dedicated to field titles... Actually a standard CSV format.'); ?></p>
    <p><?php echo __('To import the culture of your contacts, you must use the standard language code (ex: en, fr, br,...). This field is optional ("fr" by default).'); ?></p>
    <p><?php echo __('Try to import a maximum of %%nb%% lines at once. Then repeat the import the number of times needed.',array('%%nb%%' => 250)) ?></p>
    <p><?php echo __('The field "GROUPE" is used to create groups for personal contacts, and categories for professional contacts.') ?></p>
  </div>
  <form class="tdp-import" method="post" enctype="multipart/form-data" action="<?php echo url_for('contact/import') ?>">
    <p>
      <input type="file" name="rp-import" pattern="\.(csv)$" />
      <input type="submit" name="submit" value="<?php echo __('Send') ?>" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" />
    </p>
  </form>
</div>
