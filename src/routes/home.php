<?php
ob_start(); ?>
<section class="card center">
  <h1>Užsakymų priėmimo ir vykdymo sistema</h1>
  <p>Ši sistema leidžia klientams pateikti užsakymus, administratoriams juos priskirti technikams, o technikams žymėti įvykdymą.</p>
</section>
<?php view(ob_get_clean()); ?>
