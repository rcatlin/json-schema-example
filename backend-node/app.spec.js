'use strict';

const app = require('./app');

const expect = require('chai').expect;

describe('app', () => {
    describe('"an app is exported"', () => {
        it('should export an express app instance', () => {
            expect(app).to.be.a('Object');
        });
    });

    describe('"boolean"', () => {
        it('should true to be true', () => {
            expect(true).to.eq(true);
        })
    });
});
