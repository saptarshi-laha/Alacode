</div>
</div>
</div>
<footer class="bg-white sticky-footer">
<div class="container my-auto">
<div class="text-center my-auto copyright"><span>Copyright © À La Codé 2019</span></div>
</div>
</footer>
</div>
</div>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('6LeKh8cUAAAAAKF6w1y264ZzLQfzMQhjRjgVzJJa', {action: 'homepage'}).then(function(token) {
      var captchas=document.getElementsByName('captcha');
      var num=captchas.length;
      for(var i = 0; i<num; i++){
        document.getElementsByName('captcha')[i].value = token;
      }
    });
});
</script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
<script src="assets/js/theme.js"></script>
</body>

</html>
