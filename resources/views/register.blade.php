<!DOCTYPE html>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 5px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .info {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .info a {
            color: #667eea;
            text-decoration: none;
        }

        .info a:hover {
            text-decoration: underline;
        }

        .loader {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

</head>
<body>
<div class="container">
    <h1>Регистрация</h1>
    <div id="successMessage" class="success" style="display: none;">
        Регистрация прошла успешно! Переход на главную...
    </div>

    <form id="registerForm">
        <div class="form-group">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" required>
            <div class="error" id="nameError"></div>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <div class="error" id="emailError"></div>
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
            <div class="error" id="passwordError"></div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Подтверждение пароля:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <div class="error" id="password_confirmationError"></div>
        </div>
        <button type="submit">Зарегистрироваться</button>

        <div class="loader" id="loader">
            <div class="spinner"></div>
        </div>
    </form>

    <div class="info">
        Уже есть аккаунт? <a href="/login">Войти</a>
    </div>
</div>

<script>
    const form = document.getElementById('registerForm');
    const loader = document.getElementById('loader');
    const successMessage = document.getElementById('successMessage');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Очищаем ошибки
        clearErrors();
        loader.style.display = 'block';

        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            password_confirmation: document.getElementById('password_confirmation').value,
        };

        try {
            const response = await fetch('/api/user/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                // Сохраняем токен
                localStorage.setItem('token', result.data.token);
                localStorage.setItem('user', JSON.stringify(result.data.user));

                // Показываем сообщение об успехе
                successMessage.style.display = 'block';
                form.style.display = 'none';

                // Перенаправляем на главную через 2 секунды
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            } else {
                // Обрабатываем ошибки валидации
                if (result.errors) {
                    Object.keys(result.errors).forEach(field => {
                        const errorElement = document.getElementById(field + 'Error');
                        if (errorElement) {
                            errorElement.textContent = result.errors[field][0];
                        }
                    });
                } else {
                    alert(result.message || 'Ошибка при регистрации');
                }
            }
        } catch (error) {
            alert('Ошибка сети: ' + error.message);
            console.error('Error:', error);
        } finally {
            loader.style.display = 'none';
        }
    });

    function clearErrors() {
        document.querySelectorAll('.error').forEach(el => {
            el.textContent = '';
        });
    }
</script>

</body>
</html>
