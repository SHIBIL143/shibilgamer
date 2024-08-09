document.getElementById('giveaway-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const fullname = document.getElementById('fullname').value;
    const telegramUsername = document.getElementById('telegram-username').value;

    // Replace with your Telegram bot token and channel ID
    const botToken = '6730696754:AAFj8glN-U2dMMeZ80BONllbgNumfK8_qeg';
    const channelId = '@SG_BROADCAST';  // Use '@' before your channel ID

    // Use MarkdownV2 for bold text
    const message = `*New Member Participated Giveaway*\n*Name:* ${fullname}\n*Telegram Username:* ${telegramUsername}`;
    const formattedMessage = `*New\\ Member\\ Participated\\ Giveaway*\n\n*Name:*\\ ${fullname}\n*Telegram\\ Username:*@\\ ${telegramUsername}`;
    const participateLink = 'https://shibilgamer.online/giveaway';

    // Prepare the payload with the message and inline button
    const payload = {
        chat_id: channelId,
        text: formattedMessage,
        parse_mode: 'MarkdownV2',  // Use MarkdownV2 for stricter formatting
        reply_markup: {
            inline_keyboard: [[
                { text: 'Link to participate', url: participateLink }
            ]]
        }
    };

    // Send the message to the Telegram channel
    fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);  // Print the API response to debug
        if (data.ok) {
            alert('Submission successful!');
        } else {
            alert(`Error occurred: ${data.description}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error occurred, please try again.');
    });
});
