<form action="/panel/financial/charge" method="post" id="cartForm">
                            {{ csrf_field() }}
                            <input type="amount" name="amount" value="{{$amount}}">
                            <input type="gateway" name="gateway" value="{{$gateway}}">


 </form>

 
<script>

 function submitForm() {
  document.getElementById("cartForm").submit();
}
submitForm() ;
</script>