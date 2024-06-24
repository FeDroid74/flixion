document.addEventListener('DOMContentLoaded', function() {
    // Функция для выхода из аккаунта
    function logout() {
        fetch('server/logout.php')
            .then(response => {
                if (response.ok) {
                    window.location.href = 'index.html'; // Перенаправление на страницу входа
                } else {
                    console.error('Ошибка при выходе');
                }
            })
            .catch(error => console.error('Ошибка при выходе:', error));
    }

    // Добавление обработчика события для кнопки выхода
    document.querySelector('.menu-top__elements-logout').addEventListener('click', logout);

    // Форматирование баланса с копейками
    function formatBalance(balance) {
        return balance.toFixed(2).replace('.', ',');
    }

    // Функция для обновления оставшегося времени
    function updateRemainingTime(duration) {
        let totalMinutes = 0;

        // Проверка формата duration (может быть в формате "HH:MM" или числовом формате минут)
        if (typeof duration === 'string') {
            const [hours, minutes] = duration.split(':').map(Number);
            totalMinutes = (hours * 60) + minutes;
        } else {
            totalMinutes = Math.floor(duration); // Округляем до ближайшего целого числа
        }

        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        const formattedTime = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
        document.querySelector('.menu-top__elements-option.remaining-time').textContent = `Оставшееся время: ${formattedTime}`;
    }

    let costPerMinute = 1.3;
    let currentBalance = 0;
    let isComputerSelected = false;
    let selectedGame = null;
    let isTariffActive = false; // Новый флаг для проверки активного тарифа
    let tariffDuration = 0; // Переменная для хранения оставшегося времени по тарифу
    let tariffInterval; // Переменная для хранения таймера тарифа

    // Запрос на получение данных о пользователе
    fetch('server/getUserData.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user) {
                currentBalance = parseFloat(data.user.balance);
                // Обновление значения ника пользователя и баланса
                document.querySelector('.menu-top__elements-option.balance').textContent = `Баланс ${data.user.nickname}: ${formatBalance(currentBalance)} ₽`;
                document.querySelector('title').textContent = `Аккаунт ${data.user.nickname} | Flixion`;
                // Обновление оставшегося времени
                if (isTariffActive) {
                    updateRemainingTime(tariffDuration);
                } else {
                    updateRemainingTime(currentBalance / costPerMinute);
                }
            } else {
                console.error('Ошибка загрузки данных:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при запросе данных:', error);
        });

    // Показать модальное окно выбора компьютера
    function showComputerModal() {
        const modal = document.getElementById('computerModal');
        const span = document.getElementsByClassName('modal__content-close')[0];

        modal.style.display = 'block';

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    }

    // Показать модальное окно выбора игры
    function showGameModal() {
        if (!isComputerSelected) {
            showWarningModal('Пожалуйста, выберите компьютер перед выбором игры.');
            return;
        }

        const modal = document.getElementById('gameModal');
        const span = document.getElementsByClassName('modal__content-close')[1];

        modal.style.display = 'block';

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Запрос на получение данных о играх
        fetch('server/getGameData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const gameList = document.getElementById('gameList');
                    gameList.innerHTML = ''; // Очистить список перед добавлением новых элементов
                    data.games.forEach(game => {
                        const li = document.createElement('li');
                        li.textContent = game.name_game;
                        li.classList.add('modal__content-game');
                        li.onclick = function() {
                            if (currentBalance < costPerMinute && !isTariffActive) {
                                showWarningModal('Недостаточно средств на балансе для запуска игры.');
                                return;
                            }
                            selectedGame = game.name_game;
                            document.querySelector('.payment__middle-panel__elements-title.game').textContent = `Вы играете в: ${game.name_game}`;
                            updateGameData(game.id, 'start');
                            modal.style.display = 'none';
                        };
                        gameList.appendChild(li);
                    });
                } else {
                    console.error('Ошибка загрузки данных об играх:', data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка при запросе данных об играх:', error);
            });
    }

    // Показать модальное окно предупреждения
    function showWarningModal(message) {
        const modal = document.getElementById('warningModal');
        const span = document.getElementsByClassName('modal__content-close')[2];
        const messageElement = document.querySelector('.modal__content-text');

        messageElement.textContent = message;
        modal.style.display = 'block';

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    }

    // Функция для обновления цен тарифов
    function updateTariffPrices(roomId) {
        fetch('server/getTariffPrices.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ room_id: roomId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.payment__large-panel__elements').forEach((element, index) => {
                    const priceElement = element.querySelector('.price');
                    priceElement.textContent = data.tariffs[index].price;
                });
            } else {
                console.error('Ошибка загрузки цен тарифов:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при запросе цен тарифов:', error);
        });
    }

    // Запрос на получение списка свободных и включенных компьютеров
    fetch('server/getComputerData.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const computerList = document.getElementById('computerList');
                data.data.forEach(computer => {
                    if (computer.book === 'Свободен' && computer.status === 'Включен') {
                        const li = document.createElement('li');
                        li.textContent = `${computer.name_computer} (Зал «${computer.name_room}»)`;
                        li.classList.add('modal__content-computer');
                        li.onclick = function() {
                            document.querySelector('.menu-top__elements-option.selected-computer').textContent = computer.name_computer;
                            document.querySelector('.payment__middle-panel__elements-title.game').textContent = `Вы играете в: `;
                            costPerMinute = parseFloat(computer.cost);
                            isComputerSelected = true;
                            // Установка стоимости зала в сессию и обновление цен тарифов
                            fetch('server/setRoomCost.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ cost: computer.cost, room_id: computer.room_id })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    updateTariffPrices(computer.room_id);
                                    if (!isTariffActive) {
                                        updateRemainingTime(currentBalance / costPerMinute);
                                    }
                                } else {
                                    console.error('Ошибка установки стоимости зала:', data.message);
                                }
                            })
                            .catch(error => console.error('Ошибка при установке стоимости зала:', error));
                            document.getElementById('computerModal').style.display = 'none';
                        };
                        computerList.appendChild(li);
                    }
                });
                showComputerModal();
            } else {
                console.error('Ошибка загрузки данных о компьютерах:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка при запросе данных о компьютерах:', error);
        });

    // Добавление обработчика события для кнопки "Выбрать другой компьютер"
    document.querySelector('.choose-computer').addEventListener('click', showComputerModal);

    // Добавление обработчика события для кнопки "Выбрать игру"
    document.querySelector('.choose-game').addEventListener('click', showGameModal);

    // Показать модальное окно обратной связи
    function showFeedbackModal() {
        const modal = document.getElementById('feedbackModal');
        const span = document.getElementsByClassName('modal__content-close')[3];

        modal.style.display = 'block';

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    }

    // Обработка отправки формы обратной связи
    document.getElementById('feedbackForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const issueType = document.getElementById('issueType').value;
        const comment = document.getElementById('comment').value;

        fetch('server/submitFeedback.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ issue_type: issueType, comment: comment })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Ваше сообщение отправлено.');
            } else {
                alert('Ошибка отправки сообщения: ' + data.message);
            }
            document.getElementById('feedbackModal').style.display = 'none';
        })
        .catch(error => {
            alert('Ошибка отправки сообщения: ' + error.message);
            document.getElementById('feedbackModal').style.display = 'none';
        });
    });

    // Добавление обработчика события для кнопки "Обратиться к администрации"
    document.querySelector('.contact-admin').addEventListener('click', showFeedbackModal);    

    // Функция для обновления данных об игре
    function updateGameData(gameId, action) {
        fetch('server/chooseGame.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ game_id: gameId, action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error(`Ошибка при ${action} игры:`, data.message);
            }
        })
        .catch(error => {
            console.error(`Ошибка при ${action} игры:`, error);
        });
    }

    // Добавление обработчика события для кнопки "Выйти из игры"
    document.querySelector('.payment__middle-panel__elements-button.exit-game').addEventListener('click', function(event) {
        event.preventDefault();
        if (selectedGame) {
            updateGameData(selectedGame.id, 'exit');
            selectedGame = null;
            document.querySelector('.payment__middle-panel__elements-title.game').textContent = `Вы играете в: `;
        }
    });
    
    // Обработка покупки тарифных пакетов
    function buyTariff(tariffId) {
        fetch('server/buyTariff.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
             body: JSON.stringify({ tariff_id: tariffId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const balanceElement = document.querySelector('.menu-top__elements-option.balance');
                const newBalance = parseFloat(data.balance);
                currentBalance = newBalance;
                isTariffActive = true; // Установка флага активного тарифа
                tariffDuration = data.duration; // Установка времени тарифа
                balanceElement.textContent = `Баланс ${data.nickname}: ${formatBalance(newBalance)}₽`;
                document.querySelector('.payment__middle-panel__elements-title').textContent = `Активный пакет: ${data.tariff_name}`;
                // Обновление оставшегося времени на основе купленного тарифа
                updateRemainingTime(tariffDuration);
                startTariffTimer(); // Запуск таймера тарифа
            } else {
                if (data.message === 'Недостаточно средств для покупки пакета') {
                    showWarningModal('У вас недостаточно средств на балансе для покупки пакета.');
                } else {
                    console.error('Ошибка покупки пакета:', data.message);
                }
            }
        })
        .catch(error => {
            console.error('Ошибка при покупке пакета:', error);
        });
    }
    
    // Функция для уменьшения оставшегося времени тарифа каждую минуту
    function startTariffTimer() {
        if (tariffInterval) {
            clearInterval(tariffInterval); // Очистка предыдущего таймера, если он существует
        }
    
        tariffInterval = setInterval(() => {
            if (isTariffActive && tariffDuration) {
                let [hours, minutes, seconds] = tariffDuration.split(':').map(Number);
                let totalSeconds = hours * 3600 + minutes * 60 + seconds;
                totalSeconds--;
    
                if (totalSeconds <= 0) {
                    isTariffActive = false;
                    clearInterval(tariffInterval);
                    showWarningModal('Время по тарифу истекло.');
                    if (selectedGame) {
                        updateGameData(selectedGame.id, 'exit');
                        selectedGame = null;
                        document.querySelector('.payment__middle-panel__elements-title.game').textContent = `Вы играете в: `;
                    }
                } else {
                    hours = Math.floor(totalSeconds / 3600);
                    minutes = Math.floor((totalSeconds % 3600) / 60);
                    seconds = totalSeconds % 60;
                    tariffDuration = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    updateRemainingTime(tariffDuration);
                }
            }
        }, 60000); // Уменьшение времени каждую секунду
    }

    // Добавление обработчиков событий для кнопок покупки пакетов
    document.querySelectorAll('.payment__large-panel__elements-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const tariffId = this.getAttribute('data-tariff-id');
            buyTariff(tariffId);
        });
    });

    // Функция для списания баланса каждую минуту
    function chargeUserPerMinute() {
        if (!isComputerSelected || isTariffActive) return;

        fetch('server/debitUserAccount.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const balanceElement = document.querySelector('.menu-top__elements-option.balance');
                    const newBalance = parseFloat(data.balance);
                    currentBalance = newBalance;
                    balanceElement.textContent = `Баланс ${data.nickname}: ${formatBalance(newBalance)}₽`;
                    // Обновление оставшегося времени
                    updateRemainingTime(newBalance / costPerMinute);
                } else {
                    // Если недостаточно средств, автоматически выйти из игры
                    if (selectedGame) {
                        updateGameData(selectedGame.id, 'exit');
                        selectedGame = null;
                        document.querySelector('.payment__middle-panel__elements-title.game').textContent = `Вы играете в: `;
                        showWarningModal('На балансе недостаточно средств.');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка при запросе списания баланса:', error);
            });
    }
    
    // Запуск списания баланса каждую минуту
    setInterval(chargeUserPerMinute, 60000);

    // Проверка и предупреждение при запуске игры
    function checkAndStartGame(game) {
        if (currentBalance < costPerMinute && !isTariffActive) {
            showWarningModal('Недостаточно средств на балансе для запуска игры.');
            return;
        }
        selectedGame = game.name_game;
        document.querySelector('.payment__middle-panel__elements-title.game').textContent = `Вы играете в: ${game.name_game}`;
        updateGameData(game.id, 'start');
    }
});