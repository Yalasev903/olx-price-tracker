<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe</title>
</head>
<body>
    <h1>Subscribe to price tracker</h1>

    <form action="/subscribe" method="POST">
        @csrf
        <label for="url">URL:</label>
        <input type="url" id="url" name="url" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Subscribe</button>
    </form>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault(); // предотвращаем стандартное поведение формы

            $.ajax({
                url: '/subscribe',
                type: 'POST',
                data: {
                    url: $('#url').val(),
                    email: $('#email').val(),
                    _token: $('input[name="_token"]').val() // добавляем CSRF-токен
                },
                success: function(response) {
                    alert('Subscribed successfully!');
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.status);
                }
            });
        });
    });
</script>
</html>
