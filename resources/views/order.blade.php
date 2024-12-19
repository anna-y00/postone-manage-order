<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
</head>
<style>
    body {
        max-width: 350px;
        margin: 20px auto;
    }
</style>
<body>
<form id="paymentForm">
    <label for="impUid">Imp UID:</label>
    <input type="text" id="impUid" name="imp_uid" required>
    <button type="submit">Submit</button>
</form>
<div id="result"></div>
<script>
    document.getElementById('paymentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const impUid = document.getElementById('impUid').value;

        const response = await fetch('/api/payments/detail', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ imp_uid: impUid }),
        });

        const result = await response.json();
        document.getElementById('result').textContent = JSON.stringify(result, null, 2);
    });
</script>
</body>
</html>
