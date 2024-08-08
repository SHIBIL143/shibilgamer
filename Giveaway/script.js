document.getElementById('giveawayForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const fullName = document.getElementById('fullName').value;
    const telegramUsername = document.getElementById('telegramUsername').value;

    // Your Telegram Bot Token
    const botToken = '6730696754:AAFZneWcPJ7EmN6oKAd9fiGa-I4hGBd_UU0';
    // Your Telegram Chat ID (can be your ID or a group/channel ID)
    const chatId = '5007865448';

    const message = `New Giveaway Entry:\n\nFull Name: ${fullName}\nTelegram Username: @${telegramUsername}`;

    fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            chat_id: chatId,
            text: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            alert('Your entry has been submitted successfully!');
        } else {
            alert('Failed to send the message.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message.');
    });
});
