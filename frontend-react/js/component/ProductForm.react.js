import React, { Component } from 'react';
import Form from 'react-jsonschema-form';
import PropTypes from 'prop-types';
import Request from 'request';

import Header from './Header.react';
import ProductSchema from '../../../schema/product.json';

const uiSchema = {
    'id': {
        'ui:widget': 'hidden'
    },
    'ui:order': ['name', 'description', 'price', 'brand', 'id']
};


class ProductForm extends Component {
    onSubmit({formData}) {
        Request.post(
            {
                body: formData,
                json:true,
                uri: 'http://localhost:8080'
            },
            (err, resp, body) => {
                if (resp === undefined) return;
                if (resp.statusCode !== 202) return;
                console.log('Saved!');
            }
        );
    }

    render() {
        const defaultData = {
            'name': 'Foo',
            'description': 'Contains bar as well',
            'price': 55.00,
            'brand': 'Qux Inc.'
        };

        return (
            <div>
                <Header />
                <div>
                    <Form acceptcharset="ISO-8859-1"
                          action="http://localhost:{this.props.port}"
                          autocomplete="off"
                          enctype="application/json"
                          formData={defaultData}
                          method="post"
                          onSubmit={this.onSubmit}
                          schema={ProductSchema}
                          uiSchema={uiSchema} />
                </div>
            </div>
        );
    }
}

ProductForm.propTypes = {
    port: PropTypes.number.isRequired
};

export default ProductForm;
