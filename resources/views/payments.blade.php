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
        font-family: Arial, sans-serif;
    }

    form {
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    div {
        max-width: 350px;
        margin: 20px auto;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 14px;
        color: #FC6B2D;
        text-align:center;
    }

    input {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
        box-sizing: border-box;
        transition: border-color 0.3s;
        text-align: center;
    }

    input:focus {
        border-color: #FC5005;
    }

    button {
        display: block;
        width: 100%;
        padding: 10px 0;
        font-size: 16px;
        cursor: pointer;
        background-color: #FC6B2D;
        color: white;
        border: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #EF4A03;
    }

    #result {
        background-color: #f4f4f4;
        padding: 10px;
        border-radius: 4px;
        font-size: 14px;
        white-space: pre-wrap;
        color: #333;
        text-align:center;
    }
</style>
<body>
<form id="paymentForm">
    <label for="impUid">포트원 거래고유번호</label>
    <input type="text" id="impUid" name="imp_uid" required>
    <button type="submit">Submit</button>
</form>
<div id="result"></div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resultDiv = document.getElementById('result');
        resultDiv.style.display = 'none';
        document.getElementById('paymentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const impUid = document.getElementById('impUid').value;
            resultDiv.style.display = 'none';

            const response = await fetch('/api/payments/detail', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ imp_uid: impUid }),
            });

            const result = await response.json();
            if(result){
                if(result == 1){
                    alert("취소 성공");
                }else{
                    document.getElementById('result').textContent = JSON.stringify(result, null, 2);
                    resultDiv.textContent = JSON.stringify(result, null, 2);
                    resultDiv.style.display = 'block';
                }

            }

        });
    });
</script>
</body>
</html>
