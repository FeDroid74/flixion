document.getElementById('authorizationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем отправку формы по умолчанию

    // Получение ответа reCAPTCHA
    const resultDiv = document.getElementById('result');
    const recaptchaResponse = grecaptcha.getResponse();

    // Проверка, прошла ли проверка reCAPTCHA
    if (recaptchaResponse.length === 0) {
        resultDiv.style.color = '#D72325';
        resultDiv.innerHTML = 'Пожалуйста, подтвердите, что вы не робот.';
        return;
    }

    const formData = new FormData(this); // Создание объекта FormData из формы
    formData.append('g-recaptcha-response', recaptchaResponse); // Добавление ответа reCAPTCHA в FormData

    // Отправка данных формы на сервер
    fetch('server/authorization.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Преобразование ответа сервера в JSON
    .then(data => {
        console.log('Response:', data); // Логирование ответа сервера
        // Отображение результата авторизации
        resultDiv.style.color = data.success ? '#249355' : '#D72325';
        resultDiv.innerHTML = data.message;

        // Перенаправление на соответствующую страницу при успешной авторизации
        if (data.success) {
            if (data.role === 1) {
                window.location.href = 'adminPanel.html';
            } else {
                window.location.href = 'userAccount.html';
            }
        }
    })
    .catch(error => {
        // Обработка ошибок
        console.error('Error:', error);
        resultDiv.style.color = '#D72325';
        resultDiv.innerHTML = `Ошибка авторизации: ${error.message}`;
    });
});
