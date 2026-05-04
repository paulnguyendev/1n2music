<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lấy IP Nội Bộ</title>
</head>
<body>

<h2>Địa chỉ IP nội bộ của bạn:</h2>
<p id="local-ip">Đang lấy IP...</p>

    @csrf
    <input type="hidden" name="local_ip" id="local-ip-input">
    <button type="submit" onclick="">Gửi IP về Server</button>

<script>
    alert('aaaaa')
    function getLocalIP(callback) {
        const peerConnection = new RTCPeerConnection({ iceServers: [] });
        peerConnection.createDataChannel('');

        peerConnection.createOffer()
            .then((offer) => peerConnection.setLocalDescription(offer))
            .catch((error) => console.error('Error creating offer:', error));

        peerConnection.onicecandidate = (event) => {
            if (!event || !event.candidate) return;
            const ipRegex = /([0-9]{1,3}\.){3}[0-9]{1,3}/;
            const ipAddress = ipRegex.exec(event.candidate.candidate);
            if (ipAddress) {
                callback(ipAddress[0]);
                peerConnection.close();
            }
        };
    }

    getLocalIP((ip) => {
        console.log('Client IP Address:', ip);
    });
</script>

</body>
</html>
