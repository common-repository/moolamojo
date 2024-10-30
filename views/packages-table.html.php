<div class="moolamojo-packages-table">
   <?php foreach($packages as $package):?>
   <div class="moolamojo-package-box">
      <p class="package-text"><?php printf($text, $package->moola, MOOLA_CURRENCY, MOOLA_REAL_CURRENCY.$package->price);?><p>
      <form method="post">
      <p class="package-button"><input type="submit" value="<?php echo $button_text?>"></p>
      <input type="hidden" name="moolamojo_buy_package" value="1">
      <input type="hidden" name="package_id" value="<?php echo $package->id?>">
      </form> 
   </div>
   <?php endforeach;?>
</div>