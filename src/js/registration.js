document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Предотвращаем отправку формы по умолчанию

    // Получение ответа reCAPTCHA
    const resultDiv = document.getElementById('result');
    // const recaptchaResponse = grecaptcha.getResponse(); 

    // Проверка, прошла ли проверка reCAPTCHA
    // if (recaptchaResponse.length === 0) {
    //     resultDiv.style.color = '#D72325';
    //     resultDiv.innerHTML = 'Пожалуйста, подтвердите, что вы не робот.';
    //     return;
    // }

    const formData = new FormData(this); // Создание объекта FormData из формы
    // formData.append('g-recaptcha-response', recaptchaResponse); // Добавление ответа reCAPTCHA в FormData

    // Отправка данных формы на сервер
    fetch('server/registration.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Преобразование ответа сервера в текст
    .then(data => {
        try {
            // Отображение результата регистрации
            resultDiv.style.color = data.success ? '#249355' : '#D72325';
            resultDiv.innerHTML = data.message;
        } catch (error) {
            console.error('Ошибка парсинга JSON:', error, 'Response Text:', text); // Логирование ошибки парсинга и ответа
            resultDiv.style.color = '#D72325';
            resultDiv.innerHTML = `Ошибка регистрации: ${error.message}`;
        }
    })
    .catch(error => {
        // Обработка ошибок
        console.error('Ошибка регистрации:', error);
        resultDiv.style.color = '#D72325';
        resultDiv.innerHTML = `Ошибка регистрации: ${error.message}`;
    });
});
