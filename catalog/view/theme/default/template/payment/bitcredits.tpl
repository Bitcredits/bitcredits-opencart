<div id="bitcredits-payment-box">Loading...</div>
<script type="text/javascript">
//<![CDATA[
(function(){
  if (document.getElementById("BitC") == null) {
    var bitc=document.createElement('script');
    bitc.type='text/javascript';
    bitc.setAttribute("id", "BitC");
    bitc.src = '<?php echo $api_endpoint; ?>/v1/bitcredits.js';
    var s=document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(bitc,s);
  }
  window.BitCredits = window.BitCredits || [];
  window.BitCredits.push(['onConfigReady', function(){
    window.BitCredits.push(['setupOpenCart', <?php echo $orderamount; ?>, <?php echo json_encode(array(
      'firstname' => $first_name,
      'lastname' => $last_name,
      'email' => $email
    )); ?>]);
  }]);
}());
//]]>
</script>
<div class="buttons">
  <div class="right"><a id="button-confirm" class="button"><span><?php echo $button_bitcredits_confirm; ?></span></a></div>
</div>