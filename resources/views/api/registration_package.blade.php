<form action="{{ route('payRegistrationPackage') }}" method="post" id="cartForm">
    {{ csrf_field() }}
    <input name="id" value="{{ $package_id }}" type="hidden">

</form>



<script>

 function submitForm() {
  document.getElementById("cartForm").submit();
}
submitForm() ;
</script>
