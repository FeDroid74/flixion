document.addEventListener('DOMContentLoaded', function() {
    // Функция для выхода из аккаунта
    function logout() {
        fetch('server/logout.php')
            .then(response => {
                if (response.ok) {
                    window.location.href = 'index.html'; // Перенаправление на страницу входа
                }
            })
            .catch(error => console.error('Ошибка при выходе:', error));
    }

    // Добавление обработчика события для кнопки выхода
    document.querySelector('.menu-top__elements-icon').addEventListener('click', logout);

    // Функция для анимации категорий
    function setActiveClass(activeElement) {
        document.querySelectorAll('.menu-left__category').forEach(element => {
            element.classList.remove('main');
        });
        if (activeElement) {
            activeElement.classList.add('main');
        }
    }

    // Функции для переключения таблиц
    function showTable(tableIdToShow, menuElement) {
        const tables = document.querySelectorAll('.display table');
        tables.forEach(table => {
            table.style.display = 'none';
        });
        document.getElementById(tableIdToShow).style.display = 'table';
        setActiveClass(menuElement);
    }

    // Добавление обработчиков событий для переключения таблиц
    document.querySelector('.menu-left__category[onclick="showComputerTable()"]').addEventListener('click', function() {
        showTable('computerTable', this);
    });

    document.querySelector('.menu-left__category[onclick="showUserTable()"]').addEventListener('click', function() {
        showTable('userTable', this);
    });

    document.querySelector('.menu-left__category[onclick="showGameTable()"]').addEventListener('click', function() {
        showTable('gameTable', this);
    });

    document.querySelector('.menu-left__category[onclick="showEquipmentTable()"]').addEventListener('click', function() {
        showTable('equipmentTable', this);
        loadEquipmentData();
    });

    document.querySelector('.menu-left__category[onclick="showRoomTable()"]').addEventListener('click', function() {
        showTable('roomTable', this);
        loadRoomData();
    });

    document.querySelector('.menu-left__category[onclick="showTariffTable()"]').addEventListener('click', function() {
        showTable('tariffTable', this);
        loadTariffData();
    });

    document.querySelector('.menu-left__category[onclick="showFeedbackTable()"]').addEventListener('click', function() {
        showTable('feedbackTable', this);
        loadFeedbackData();
    });

    // Функция для загрузки данных обратной связи
    function loadFeedbackData() {
        fetch('server/getFeedbackData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const feedbackTableBody = document.getElementById('feedbackTable').getElementsByTagName('tbody')[0];
                    feedbackTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.feedback.forEach(feedback => {
                        const row = feedbackTableBody.insertRow();
                        row.insertCell(0).textContent = feedback.username;
                        row.insertCell(1).textContent = feedback.issue_type;
                        row.insertCell(2).textContent = feedback.comment;
                        row.insertCell(3).textContent = formatDate(feedback.submission_date);
                        const actionsCell = row.insertCell(4);
                        actionsCell.innerHTML = `
                            <span class="icon-button plus" onclick="addFeedback()">&#xFF0B;</span>
                            <span class="icon-button" onclick="editFeedback(${feedback.id})">&#9998;</span>
                            <span class="icon-button" onclick="deleteFeedback(${feedback.id})">&#x2716;</span>
                        `;
                    });
                } else {
                    console.error('Ошибка загрузки данных обратной связи: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка: ' + error.message);
            });
    }

    // Функция для форматирования даты
    function formatDate(dateString) {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString('ru-RU', options);
    }

    // Запрос на получение данных о оборудовании
    function loadAllEquipmentData() {
        fetch('server/getAllEquipmentData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const equipmentTableBody = document.getElementById('equipmentTable').getElementsByTagName('tbody')[0];
                    equipmentTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.equipment.forEach(equipment => {
                        const row = equipmentTableBody.insertRow();
                        row.insertCell(0).textContent = equipment.type;
                        row.insertCell(1).textContent = equipment.graphicscard;
                        row.insertCell(2).textContent = equipment.cpu;
                        row.insertCell(3).textContent = equipment.ram;
                        row.insertCell(4).textContent = equipment.motherboard;
                        row.insertCell(5).textContent = equipment.monitor;
                        row.insertCell(6).textContent = equipment.keyboard;
                        row.insertCell(7).textContent = equipment.mouse;
                        const editActionsCell = row.insertCell(8);
                        editActionsCell.innerHTML = `
                            <span class="icon-button plus" onclick="addEquipment()">&#xFF0B;</span>
                            <span class="icon-button" onclick="editEquipment(${equipment.id})">&#9998;</span>
                            <span class="icon-button" onclick="deleteEquipment(${equipment.id}})">&#x2716;</span>
                        `;
                    });
                } else {
                    console.error('Ошибка загрузки данных оборудования: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных оборудования:', error);
            });
    }

    // Функция для отображения оборудования компьютера
    window.showEquipment = function(computerId) {
        fetch(`server/getEquipmentData.php?computer_id=${computerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const equipment = data.equipment;
                    const equipmentTableBody = document.getElementById('equipmentTable').getElementsByTagName('tbody')[0];
                    equipmentTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    const row = equipmentTableBody.insertRow();
                    row.insertCell(0).textContent = equipment.type;
                    row.insertCell(1).textContent = equipment.graphicscard;
                    row.insertCell(2).textContent = equipment.cpu;
                    row.insertCell(3).textContent = equipment.ram;
                    row.insertCell(4).textContent = equipment.motherboard;
                    row.insertCell(5).textContent = equipment.monitor;
                    row.insertCell(6).textContent = equipment.keyboard;
                    row.insertCell(7).textContent = equipment.mouse;
                    const editActionsCell = row.insertCell(8);
                    editActionsCell.innerHTML = `
                        <span class="icon-button plus" onclick="addEquipment()">&#xFF0B;</span>
                        <span class="icon-button" onclick="editEquipment(${equipment.id})">&#9998;</span>
                        <span class="icon-button" onclick="deleteEquipment(${equipment.id}})">&#x2716;</span>
                    `;
                    showTable('equipmentTable', null);
                } else {
                    alert('Ошибка получения данных об оборудовании');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
    }

    // Запрос на получение данных о компьютерах
    function loadComputerData() {
        fetch('server/getComputerData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновление значений на панели
                    document.querySelector('.menu-left__options-title.red').textContent = `Заняты: ${data.occupied}`;
                    document.querySelector('.menu-left__options-title.green').textContent = `Свободны: ${data.available}`;
                    document.querySelector('.menu-left__options-title:not(.red):not(.green):not(.grey)').textContent = `Включены: ${data.powered_on}`;
                    document.querySelector('.menu-left__options-title.grey').textContent = `Выключены: ${data.powered_off}`;

                    // Обновление таблицы с компьютерами
                    const computerTableBody = document.getElementById('computerTable').getElementsByTagName('tbody')[0];
                    computerTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.data.forEach(computer => {
                        const row = computerTableBody.insertRow();
                        row.insertCell(0).textContent = computer.name_computer;
                        row.insertCell(1).textContent = computer.name_room;
                        row.insertCell(2).textContent = computer.status;
                        row.insertCell(3).textContent = computer.book;
                        const actionsCell = row.insertCell(4);
                        actionsCell.innerHTML = `<button class="display-button" onclick="showEquipment(${computer.id})">Посмотреть</button>`;
                        const userActionsCell = row.insertCell(5);
                        userActionsCell.innerHTML = `<button class="display-button" onclick="showUserComputer(${computer.id})">Посмотреть</button>`;
                        const editActionsCell = row.insertCell(6);
                        editActionsCell.innerHTML = `
                            <span class="icon-button plus" onclick="addComputer()">&#xFF0B;</span>
                            <span class="icon-button" onclick="editComputer(${computer.id})">&#9998;</span>
                            <span class="icon-button" onclick="deleteComputer(${computer.id})">&#x2716;</span>
                        `;
                    });
                } else {
                    console.error('Ошибка загрузки данных: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка: ' + error.message);
            });
    }

    // Запрос на получение данных о пользователях
    function loadUserData() {
        fetch('server/getUserData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновление имени администратора
                    document.querySelector('.menu-top__elements-title').textContent = `Администратор: ${data.nickname}`;
    
                    const userTableBody = document.getElementById('userTable').getElementsByTagName('tbody')[0];
                    userTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.users.forEach(user => {
                        const row = userTableBody.insertRow();
                        row.insertCell(0).textContent = user.nickname;
                        row.insertCell(1).textContent = user.telnum;
                        row.insertCell(2).textContent = user.email;
                        row.insertCell(3).textContent = user.role;
                        row.insertCell(4).textContent = parseFloat(user.balance).toFixed(2) + ' ₽';
                        row.insertCell(5).textContent = formatDate(user.registration_date);
                        const actionsCell = row.insertCell(6);
                        actionsCell.innerHTML = `
                            <span class="icon-button plus" onclick="addUser()">&#xFF0B;</span>
                            <span class="icon-button" onclick="editUser(${user.id})">&#9998;</span>
                            <span class="icon-button" onclick="deleteUser(${user.id})">&#x2716;</span>
                        `;
                    });
                } else {
                    console.error('Ошибка загрузки данных пользователей: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных пользователей:', error);
            });
    }

    // Запрос на получение данных об играх
    function loadGameData() {
        fetch('server/getGameData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const gameTableBody = document.getElementById('gameTable').getElementsByTagName('tbody')[0];
                    gameTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.games.forEach(game => {
                        const row = gameTableBody.insertRow();
                        row.insertCell(0).textContent = game.name_game;
                        row.insertCell(1).textContent = game.description;
                        row.insertCell(2).textContent = game.developer;
                        row.insertCell(3).textContent = game.publisher;
                        row.insertCell(4).textContent = game.times_ordered;
                        const actionsCell = row.insertCell(5);
                        actionsCell.innerHTML = `
                            <span class="icon-button plus" onclick="addGame()">&#xFF0B;</span>
                            <span class="icon-button" onclick="editGame(${game.id})">&#9998;</span>
                            <span class="icon-button" onclick="deleteGame(${game.id})">&#x2716;</span>
                        `;
                    });
                } else {
                    console.error('Ошибка загрузки данных игр: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных игр:', error);
            });
    }

    // Запрос на получение данных о залах
    function loadRoomData() {
        fetch('server/getRoomData.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const roomTableBody = document.getElementById('roomTable').getElementsByTagName('tbody')[0];
                    roomTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.rooms.forEach(room => {
                        const row = roomTableBody.insertRow();
                        row.insertCell(0).textContent = room.name_room;
                        row.insertCell(1).textContent = parseFloat(room.cost).toFixed(2) + ' ₽';
                        const actionsCell = row.insertCell(2);
                        actionsCell.innerHTML = `
                            <span class="icon-button plus" onclick="addRoom()">&#xFF0B;</span>
                            <span class="icon-button" onclick="editRoom(${room.id})">&#9998;</span>
                            <span class="icon-button" onclick="deleteRoom(${room.id})">&#x2716;</span>
                        `;
                    });
                } else {
                    console.error('Ошибка загрузки данных залов: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных залов:', error);
            });
    }

    // Запрос на получение данных о тарифах
    function loadTariffData() {
        fetch('server/getTariffData.php')
            .then(response => response.json())
            .then(data => {
            if (data.success) {
                const tariffTableBody = document.getElementById('tariffTable').getElementsByTagName('tbody')[0];
                tariffTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                data.tariffs.forEach(tariff => {
                    const row = tariffTableBody.insertRow();
                    row.insertCell(0).textContent = tariff.name_tariff;
                    row.insertCell(1).textContent = parseFloat(tariff.price).toFixed(2);
                    row.insertCell(2).textContent = tariff.duration;
                    const userActionsCell = row.insertCell(3);
                    userActionsCell.innerHTML = `<button class="display-button" onclick="showUserTariff(${tariff.id})">Посмотреть</button>`;
                    const actionsCell = row.insertCell(4);
                    actionsCell.innerHTML = `
                        <span class="icon-button plus" onclick="addTariff()">&#xFF0B;</span>
                        <span class="icon-button" onclick="editTariff(${tariff.id})">&#9998;</span>
                        <span class="icon-button" onclick="deleteTariff(${tariff.id})">&#x2716;</span> 
                    `;
                });
            } else {
                console.error('Ошибка загрузки данных тарифов: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки данных тарифов:', error);
        });
    }

    // Функция для загрузки данных пользователей, выбравших компьютер
    window.showUserComputer = function(computerId) {
        fetch(`server/getUserComputerData.php?id_computer=${computerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Полученные данные о пользователях компьютера:', data.userComputers);
                    const userComputerTableBody = document.getElementById('userComputerTable').getElementsByTagName('tbody')[0];
                    userComputerTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.userComputers.forEach(userComputer => {
                        const row = userComputerTableBody.insertRow();
                        row.insertCell(0).textContent = userComputer.nickname;
                        row.insertCell(1).textContent = userComputer.name_computer;
                        row.insertCell(2).textContent = formatDate(userComputer.start_time);
                        row.insertCell(3).textContent = formatDate(userComputer.end_time);
                    });
                    showTable('userComputerTable', null);
                } else {
                    console.error('Ошибка загрузки данных выбранных компьютеров: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных выбранных компьютеров:', error);
            });
    }

    // Функция для загрузки данных пользователей, выбравших тариф
    window.showUserTariff = function(tariffId) {
        fetch(`server/getUserTariffData.php?id_tariff=${tariffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const userTariffTableBody = document.getElementById('userTariffTable').getElementsByTagName('tbody')[0];
                    userTariffTableBody.innerHTML = ''; // Очистка таблицы перед обновлением
                    data.userTariffs.forEach(userTariff => {
                        const row = userTariffTableBody.insertRow();
                        row.insertCell(0).textContent = userTariff.nickname;
                        row.insertCell(1).textContent = userTariff.name_tariff;
                        row.insertCell(2).textContent = formatDate(userTariff.chosen_date);
                    });
                    showTable('userTariffTable', null);
                } else {
                    console.error('Ошибка загрузки данных выбранных тарифов: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных выбранных тарифов:', error);
            });
    }
    
    // Общая функция для открытия модального окна
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        const span = modal.getElementsByClassName('modal__content-close')[0];

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

    // Функция для добавления нового компьютера
    window.addComputer = function() {
        showModal('computerModal');
        document.getElementById('computerId').value = ''; // Очищаем id, чтобы указать, что это новый компьютер
        document.getElementById('computerForm').reset(); // Сбрасываем форму
    }

    // Функция для редактирования компьютера
    window.editComputer = function(computerId) {
        showModal('computerModal');
        fetch(`server/getComputerDetails.php?id_computer=${computerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('computerId').value = data.computer.id;
                    document.getElementById('computerName').value = data.computer.name_computer;
                    document.getElementById('computerRoom').value = data.computer.name_room;
                    document.getElementById('computerStatus').value = data.computer.status == 'Включен' ? 1 : 0;
                    document.getElementById('computerBook').value = data.computer.book == 'Занят' ? 1 : 0;
                } else {
                    console.error('Ошибка загрузки данных компьютера: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных компьютера:', error);
            });
    }

    // Функция для удаления компьютера
    window.deleteComputer = function(computerId) {
        if (confirm('Вы уверены, что хотите удалить этот компьютер?')) {
            fetch(`server/deleteComputer.php?id_computer=${computerId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Компьютер успешно удален');
                    loadComputerData();
                } else {
                    console.error('Ошибка удаления компьютера: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления компьютера:', error);
            });
        }
    }

    // Обработчик для формы редактирования и добавления компьютера
    document.getElementById('computerForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const computerId = document.getElementById('computerId').value;
        const computerName = document.getElementById('computerName').value;
        const computerRoom = document.getElementById('computerRoom').value;
        const computerStatus = document.getElementById('computerStatus').value;
        const computerBook = document.getElementById('computerBook').value;

        const data = {
            id: computerId,
            name_computer: computerName,
            id_room: computerRoom,
            status: computerStatus,
            book: computerBook
        };

        fetch(computerId ? 'server/updateComputer.php' : 'server/addComputer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(computerId ? 'Компьютер успешно обновлен' : 'Компьютер успешно добавлен');
                document.getElementById('computerModal').style.display = 'none';
                loadComputerData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

   // Функция для добавления нового пользователя
    window.addUser = function() {
        showModal('userModal');
        document.getElementById('userId').value = ''; // Очищаем id, чтобы указать, что это новый пользователь
        document.getElementById('userForm').reset(); // Сбрасываем форму
    }

    // Функция для редактирования пользователя
    window.editUser = function(userId) {
        console.log(`Открытие модального окна для редактирования пользователя с ID: ${userId}`);
        showModal('userModal');
    
        fetch(`server/getUserDetails.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('userId').value = data.user.id;
                    document.getElementById('userName').value = data.user.nickname;
                    document.getElementById('userTelnum').value = data.user.telnum;
                    document.getElementById('userEmail').value = data.user.email;
                    document.getElementById('userRole').value = data.user.role;
                    document.getElementById('userBalance').value = parseFloat(data.user.balance).toFixed(2);
                } else {
                    console.error('Ошибка загрузки данных пользователя: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных пользователя:', error);
            });
    }

    // Функция для удаления пользователя
    window.deleteUser = function(userId) {
        if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
            fetch(`server/deleteUser.php?id=${userId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Пользователь успешно удален');
                    loadUserData();
                } else {
                    console.error('Ошибка удаления пользователя: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления пользователя:', error);
            });
        }
    }

    // Обработчик для формы редактирования и добавления пользователя
    document.getElementById('userForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        const userId = document.getElementById('userId').value;
        const userName = document.getElementById('userName').value;
        const userTel = document.getElementById('userTel').value;
        const userEmail = document.getElementById('userEmail').value;
        const userRole = document.getElementById('userRole').value;
        const userBalance = document.getElementById('userBalance').value;
    
        const data = {
            id: userId,
            nickname: userName,
            telnum: userTel,
            email: userEmail,
            role: userRole,
            balance: userBalance
        };
    
        fetch(userId ? 'server/updateUser.php' : 'server/addUser.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(userId ? 'Пользователь успешно обновлен' : 'Пользователь успешно добавлен');
                document.getElementById('userModal').style.display = 'none';
                loadUserData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

   // Функция для добавления новой игры
    window.addGame = function() {
        showModal('gameModal');
        document.getElementById('gameId').value = ''; // Очищаем id, чтобы указать, что это новая игра
        document.getElementById('gameForm').reset(); // Сбрасываем форму
    }

    // Функция для редактирования игры
    window.editGame = function(gameId) {
        showModal('gameModal');
        fetch(`server/getGameDetails.php?id_game=${gameId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('gameId').value = data.game.id;
                    document.getElementById('gameName').value = data.game.name_game;
                    document.getElementById('gameDescription').value = data.game.description;
                    document.getElementById('gameDeveloper').value = data.game.developer;
                    document.getElementById('gamePublisher').value = data.game.publisher;
                } else {
                    console.error('Ошибка загрузки данных игры: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных игры:', error);
            });
    }
    
    // Функция для удаления игры
    window.deleteGame = function(gameId) {
        if (confirm('Вы уверены, что хотите удалить эту игру?')) {
            fetch(`server/deleteGame.php?id_game=${gameId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Игра успешно удалена');
                    loadGameData();
                } else {
                    console.error('Ошибка удаления игры: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления игры:', error);
            });
        }
    }
    
    // Обработчик для формы редактирования и добавления игры
    document.getElementById('gameForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const gameId = document.getElementById('gameId').value;
        const gameName = document.getElementById('gameName').value;
        const gameDescription = document.getElementById('gameDescription').value;
        const gameDeveloper = document.getElementById('gameDeveloper').value;
        const gamePublisher = document.getElementById('gamePublisher').value;
    
        const data = {
            id: gameId,
            name_game: gameName,
            description: gameDescription,
            developer: gameDeveloper,
            publisher: gamePublisher
        };
    
        fetch(gameId ? 'server/updateGame.php' : 'server/addGame.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(gameId ? 'Игра успешно обновлена' : 'Игра успешно добавлена');
                document.getElementById('gameModal').style.display = 'none';
                loadGameData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

    // Функция для добавления нового оборудования
    window.addEquipment = function() {
        showModal('equipmentModal');
        document.getElementById('equipmentId').value = ''; // Очищаем id, чтобы указать, что это новое оборудование
        document.getElementById('equipmentForm').reset(); // Сбрасываем форму
    }

    // Функция для редактирования оборудования
    window.editEquipment = function(equipmentId) {
        showModal('equipmentModal');
        fetch(`server/getEquipmentDetails.php?id_equipment=${equipmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('equipmentId').value = data.equipment.id;
                    document.getElementById('equipmentType').value = data.equipment.type;
                    document.getElementById('equipmentGraphicscard').value = data.equipment.graphicscard;
                    document.getElementById('equipmentCpu').value = data.equipment.cpu;
                    document.getElementById('equipmentRam').value = data.equipment.ram;
                    document.getElementById('equipmentMotherboard').value = data.equipment.motherboard;
                    document.getElementById('equipmentMonitor').value = data.equipment.monitor;
                    document.getElementById('equipmentKeyboard').value = data.equipment.keyboard;
                    document.getElementById('equipmentMouse').value = data.equipment.mouse;
                } else {
                    console.error('Ошибка загрузки данных оборудования: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных оборудования:', error);
            });
    }

    // Функция для удаления оборудования
    window.deleteEquipment = function(equipmentId) {
        if (confirm('Вы уверены, что хотите удалить это оборудование?')) {
            fetch(`server/deleteEquipment.php?id_equipment=${equipmentId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Оборудование успешно удалено');
                    loadAllEquipmentData();
                } else {
                    console.error('Ошибка удаления оборудования: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления оборудования:', error);
            });
        }
    }

    // Обработчик для формы редактирования и добавления оборудования
    document.getElementById('equipmentForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const equipmentId = document.getElementById('equipmentId').value;
        const equipmentType = document.getElementById('equipmentType').value;
        const equipmentGraphicscard = document.getElementById('equipmentGraphicscard').value;
        const equipmentCpu = document.getElementById('equipmentCpu').value;
        const equipmentRam = document.getElementById('equipmentRam').value;
        const equipmentMotherboard = document.getElementById('equipmentMotherboard').value;
        const equipmentMonitor = document.getElementById('equipmentMonitor').value;
        const equipmentKeyboard = document.getElementById('equipmentKeyboard').value;
        const equipmentMouse = document.getElementById('equipmentMouse').value;

        const data = {
            id: equipmentId,
            type: equipmentType,
            graphicscard: equipmentGraphicscard,
            cpu: equipmentCpu,
            ram: equipmentRam,
            motherboard: equipmentMotherboard,
            monitor: equipmentMonitor,
            keyboard: equipmentKeyboard,
            mouse: equipmentMouse
        };

        fetch(equipmentId ? 'server/updateEquipment.php' : 'server/addEquipment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(equipmentId ? 'Оборудование успешно обновлено' : 'Оборудование успешно добавлено');
                document.getElementById('equipmentModal').style.display = 'none';
                loadAllEquipmentData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

    // Функция для добавления нового зала
    window.addRoom = function() {
        showModal('roomModal');
        document.getElementById('roomId').value = ''; // Очищаем id, чтобы указать, что это новый зал
        document.getElementById('roomForm').reset(); // Сбрасываем форму
    }

    // Функция для редактирования зала
    window.editRoom = function(roomId) {
        showModal('roomModal');
        fetch(`server/getRoomDetails.php?id_room=${roomId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('roomId').value = data.room.id;
                    document.getElementById('roomName').value = data.room.name_room;
                    document.getElementById('roomCost').value = parseFloat(data.room.cost).toFixed(2);
                } else {
                    console.error('Ошибка загрузки данных зала: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных зала:', error);
            });
    }

    // Функция для удаления зала
    window.deleteRoom = function(roomId) {
        if (confirm('Вы уверены, что хотите удалить этот зал?')) {
            fetch(`server/deleteRoom.php?id_room=${roomId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Зал успешно удален');
                    loadRoomData();
                } else {
                    console.error('Ошибка удаления зала: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления зала:', error);
            });
        }
    }

    // Обработчик для формы редактирования и добавления зала
    document.getElementById('roomForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const roomId = document.getElementById('roomId').value;
        const roomName = document.getElementById('roomName').value;
        const roomCost = document.getElementById('roomCost').value;

        const data = {
            id: roomId,
            name_room: roomName,
            cost: roomCost
        };

        fetch(roomId ? 'server/updateRoom.php' : 'server/addRoom.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(roomId ? 'Зал успешно обновлен' : 'Зал успешно добавлен');
                document.getElementById('roomModal').style.display = 'none';
                loadRoomData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

    // Функция для добавления нового тарифа
    window.addTariff = function() {
        showModal('tariffModal');
        document.getElementById('tariffId').value = ''; // Очищаем id, чтобы указать, что это новый тариф
        document.getElementById('tariffForm').reset(); // Сбрасываем форму
    }
    
    // Функция для редактирования тарифа
    window.editTariff = function(tariffId) {
        showModal('tariffModal');
        fetch(`server/getTariffDetails.php?id_tariff=${tariffId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('tariffId').value = data.tariff.id;
                    document.getElementById('tariffName').value = data.tariff.name_tariff;
                    document.getElementById('tariffPrice').value = parseFloat(data.tariff.price).toFixed(2);
                    document.getElementById('tariffDuration').value = data.tariff.duration;
                } else {
                    console.error('Ошибка загрузки данных тарифа: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных тарифа:', error);
            });
    }

    // Функция для удаления тарифа
    window.deleteTariff = function(tariffId) {
        if (confirm('Вы уверены, что хотите удалить этот тариф?')) {
            fetch(`server/deleteTariff.php?id_tariff=${tariffId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Тариф успешно удален');
                    loadTariffData();
                } else {
                    console.error('Ошибка удаления тарифа: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления тарифа:', error);
            });
        }
    }

   // Обработчик для формы редактирования и добавления тарифа
    document.getElementById('tariffForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const tariffId = document.getElementById('tariffId').value;
        const tariffName = document.getElementById('tariffName').value;
        const tariffPrice = document.getElementById('tariffPrice').value;
        const tariffDuration = document.getElementById('tariffDuration').value;

        const data = {
            id: tariffId,
            name_tariff: tariffName,
            price: tariffPrice,
            duration: tariffDuration
        };

        fetch(tariffId ? 'server/updateTariff.php' : 'server/addTariff.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(tariffId ? 'Тариф успешно обновлен' : 'Тариф успешно добавлен');
                document.getElementById('tariffModal').style.display = 'none';
                loadTariffData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

    // Функция для добавления новой обратной связи
    window.addFeedback = function() {
        showModal('feedbackModal');
        document.getElementById('feedbackId').value = ''; // Очищаем id, чтобы указать, что это новая запись
        document.getElementById('feedbackForm').reset(); // Сбрасываем форму
    }

    // Функция для редактирования обратной связи
    window.editFeedback = function(feedbackId) {
        showModal('feedbackModal');
        fetch(`server/getFeedbackDetails.php?id_feedback=${feedbackId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('feedbackId').value = data.feedback.id;
                    document.getElementById('feedbackUsername').value = data.feedback.username;
                    document.getElementById('feedbackIssueType').value = data.feedback.issue_type;
                    document.getElementById('feedbackComment').value = data.feedback.comment;
                    document.getElementById('feedbackDate').value = formatDate(data.feedback.submission_date);
                } else {
                    console.error('Ошибка загрузки данных обратной связи: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки данных обратной связи:', error);
            });
    }    

    // Функция для удаления обратной связи
    window.deleteFeedback = function(feedbackId) {
        if (confirm('Вы уверены, что хотите удалить эту запись?')) {
            fetch(`server/deleteFeedback.php?id_feedback=${feedbackId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Запись успешно удалена');
                    loadFeedbackData();
                } else {
                    console.error('Ошибка удаления записи: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка удаления записи:', error);
            });
        }
    }

    // Обработчик для формы редактирования и добавления обратной связи
    document.getElementById('feedbackForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const feedbackId = document.getElementById('feedbackId').value;
        const feedbackUsername = document.getElementById('feedbackUsername').value;
        const feedbackIssueType = document.getElementById('feedbackIssueType').value;
        const feedbackComment = document.getElementById('feedbackComment').value;
        const feedbackDate = document.getElementById('feedbackDate').value;

        const data = {
            id: feedbackId,
            username: feedbackUsername,
            issue_type: feedbackIssueType,
            comment: feedbackComment,
            submission_date: feedbackDate
        };

        fetch(feedbackId ? 'server/updateFeedback.php' : 'server/addFeedback.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(feedbackId ? 'Запись успешно обновлена' : 'Запись успешно добавлена');
                document.getElementById('feedbackModal').style.display = 'none';
                loadFeedbackData();
            } else {
                console.error('Ошибка:', data.message);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    });

    // Закрытие модальных окон при клике вне их области
    window.onclick = function(event) {
        const gameModal = document.getElementById('gameModal');
        const tariffModal = document.getElementById('tariffModal');
        if (event.target == gameModal) {
            gameModal.style.display = 'none';
        }
        if (event.target == tariffModal) {
            tariffModal.style.display = 'none';
        }
    }

    // Загрузка данных при загрузке страницы
    loadFeedbackData()
    loadComputerData();
    loadUserData();
    loadGameData();
    loadAllEquipmentData();
    loadTariffData();
    loadRoomData();
});

// Функции для переключения таблиц
function showComputerTable() {
    document.getElementById('computerTable').style.display = 'table';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showUserTable() {
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'table';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showGameTable() {
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'table';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showFeedbackTable() {
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'table';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showTariffTable() {
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'table';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showUserTariffTable() {
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'table';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showUserComputerTable() {
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'table';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showRoomTable() {
    document.getElementById('roomTable').style.display = 'table';
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
    document.getElementById('equipmentTable').style.display = 'none';
}

function showEquipmentTable() {
    document.getElementById('equipmentTable').style.display = 'table';
    document.getElementById('computerTable').style.display = 'none';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('gameTable').style.display = 'none';
    document.getElementById('feedbackTable').style.display = 'none';
    document.getElementById('tariffTable').style.display = 'none';
    document.getElementById('roomTable').style.display = 'none';
    document.getElementById('userTariffTable').style.display = 'none';
    document.getElementById('userComputerTable').style.display = 'none';
}