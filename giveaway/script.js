document.getElementById('giveaway-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const fullname = document.getElementById('fullname').value;
    const telegramUsername = document.getElementById('telegram-username').value;

    // Replace with your Telegram bot token and chat ID
    const botToken = '6730696754:AAFZneWcPJ7EmN6oKAd9fiGa-I4hGBd_UU0';
    const chatId = '5007865448';

    const message = `Full Name: ${fullname}\nTelegram Username: ${telegramUsername}`;
    const url = `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${encodeURIComponent(message)}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                alert('Submission successful!');
            } else {
                alert('Error occurred, please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error occurred, please try again.');
        });
});
