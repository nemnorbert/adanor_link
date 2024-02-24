const express = require('express');
const app = express();
const axios = require('axios');
const errorHandler = require('./utilities/errorHandler.js');

app.get('/', (req, res, next) => {
    const link = `https://adanor.eu/link`;
    res.redirect(302, link);
})

app.get('/:id', async (req, res, next) => {
    try {
        const id = req.params.id;
        const subid = req.query.id || null;

        const isProduction = process.env.NODE_ENV === 'production';
        let fullUrl = isProduction ? 'https://api.adanor.eu' : 'http://localhost:8080';
        fullUrl += `/links/${id}`;

        const params = new URLSearchParams();
        if (subid) {params.append('id', subid);}

        const browserLang = req.headers['accept-language']?.substring(0, 2);
        if (browserLang) {params.append('lang', browserLang);}

        const queryString = params.toString() ?? null;
        if (queryString) {fullUrl += `?${queryString}`;}

        const response = await axios.get(fullUrl);
        const data = response.data;

        res.redirect(302, data.redirect_url);
    } catch (error) {
        if (error.code === 'ECONNREFUSED') {
            return errorHandler(503, 'API Service Unavailable', req, res);
        } else if (error.response) {
            return errorHandler(error.response.status, error.response.data.message, req, res);
        } else {
            console.error(error);
            return errorHandler(500, 'Internal Server Error', req, res);
        }
    }
})


module.exports = app;