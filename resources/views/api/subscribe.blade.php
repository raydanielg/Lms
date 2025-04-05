<form action="/panel/financial/pay-subscribes" method="post" id="cartForm">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$id}}">
                            <input type="hidden" name="amount" value="{{$amount}}">


 </form>

 
<script>

 function submitForm() {
  document.getElementById("cartForm").submit();
}
submitForm() ;
</script>