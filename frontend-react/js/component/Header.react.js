import React, { Component } from 'react';
import { Link } from 'react-router-dom';

export default class Header extends Component {
    render() {
        return (
            <div>
                <ul>
                    <li>
                        <Link to="/">Product Form</Link>
                    </li>
                    <li>
                        <Link to="/list">Product List</Link>
                    </li>
                </ul>
            </div>
        );
    }
}
