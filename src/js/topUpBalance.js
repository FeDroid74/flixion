document.addEventListener('DOMContentLoaded', function() {
    const balanceInput = document.getElementById('inputBalance');
    document.getElementById('topUpBalanceButton').addEventListener('click', function(event) {
        event.preventDefault();
        
        const phone = document.getElementById('inputPhone').value;
        const balance = parseFloat(balanceInput.value.replace(/[^\d,]/g, '').replace(',', '.'));

        if (phone && !isNaN(balance)) {
            const data = { telnum: phone, balance: balance };
            
            fetch('server/topUpBalance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Баланс успешно пополнен');
                } else {
                    alert('Ошибка пополнения баланса: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        } else {
            alert('Пожалуйста, введите корректные данные');
        }
    });
});