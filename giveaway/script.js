document.getElementById('watchAdButton').addEventListener('click', function() {
    // Show the modal with the Google Ad
    document.getElementById('rewardAdModal').style.display = 'block';
});

document.querySelector('.close').addEventListener('click', function() {
    // Close the modal
    document.getElementById('rewardAdModal').style.display = 'none';
});

document.getElementById('adCompleteButton').addEventListener('click', function() {
    // Close the modal and enable form submission
    document.getElementById('rewardAdModal').style.display = 'none';

    // Replace the Watch Ad button with the Submit button
    document.getElementById('watchAdButton').type = 'submit';
    document.getElementById('watchAdButton').innerText = 'Enter Giveaway';
});

// Code for submitting the form and sending data to Telegram (same as before)
document.getElementById('giveawayForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const fullName = document.getElementById('fullName').value;
    const telegramUsername = document.getElementById('telegramUsername').value;

    const botToken = '6730696754:AAFZneWcPJ7EmN6oKAd9fiGa-I4hGBd_UU0';
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
