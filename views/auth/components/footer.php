<script>
    // Toggle the password field type between text and password to check inserted value.
    function togglePasswordInputType(element) {
        const target = element.nextElementSibling;
        const inputType = target.type;

        if (inputType === 'password') {
            target.type = 'test';
            return;
        }

        target.type = 'password';
    }
</script>
</body>

</html>