<div class="container">
  <h1>Welcome to Bii!</h1>
  <p>
    Here is a table of test items inside of
    <a href="https://datatables.net/">Datatables</a>
  </p>



  <?= $this->renderView('_templates/table.php',array(
    'items' => $items,
  )); ?>


</div>
<script type="text/javascript">
  jQuery(document).ready(function(){
    $('table').DataTable({

    });
  });
</script>
