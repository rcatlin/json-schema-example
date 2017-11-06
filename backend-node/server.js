var cors = require('cors'),
    express = require('express'),
    fs = require('fs'),
    bodyParser = require('body-parser'),
    Ajv = require('ajv'),
    app = express(),
    ajv = new Ajv({allErrors: true}),
    schema = JSON.parse(fs.readFileSync(__dirname + '/../schema/product.json', 'utf8')),
    store = {},
    currentId = 0,
    PORT = 3000;

// Setup JSON Schema Validator
const validate = ajv.compile(schema);

app.use(bodyParser.json());
app.use(cors());

// Endpoint: List All Products
app.get('/', function (req, res) {
    data = [];
    for (key in store) {
        data.push(store[key]);
    }

    res.json(data);
});

// Endpoint: Get Product By ID
app.get('/:id', function (req, res) {
    if (!store.hasOwnProperty(req.params.id)) {
        res.status(404).send('Product not found');
        return;
    }

    res.json(store[req.params.id]);
});

// Endpoint: Store Product
app.post('/', function (req, res) {
    // Validate Request Data
    valid = validate(req.body);
    if (!valid) {
        res.status(400).json(validate.errors);
        return;
    }

    // Store
    store[++currentId] = {id: currentId, ...req.body};

    // Validate Response Data
    if (!validate(store[currentId])) {
        res.status(400).send('Internal data fails validation');
        return;
    }

    // Send Data
    res.status(202).send(store[currentId]);
});

console.log("Server running at http://127.0.0.1:" + PORT);

app.listen(PORT);
