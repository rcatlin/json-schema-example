var cors = require('cors'),
    express = require('express'),
    app = express(),
    PORT = 8000;

app.use(cors());
app.use('/js', express.static('public/js'));
app.use('/css', express.static('public/css'));

app.get('/*', function (req, res) {
    res.status(200)
        .sendFile(__dirname + '/public/index.html');
});

console.log("Server running at http://127.0.0.1:" + PORT);

app.listen(PORT);
