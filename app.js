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
        const baseURL = isProduction ? 'https://api.adanor.eu' : 'http://localhost:8080';
        let fullUrl = `${baseURL}/links/${id}`;
        if (subid) {fullUrl += `?id=${subid}`;}

        const response = await axios.get(fullUrl);
        const data = response.data;

        res.redirect(302, data.redirect_url);
    } catch (error) {
        if (error.code === 'ECONNREFUSED') {
            return errorHandler(503, 'API Service Unavailable', res);
        } else if (error.response) {
            return errorHandler(error.response.status, error.response.data.message, res);
        } else {
            console.error(error);
            return errorHandler(500, 'Internal Server Error', res);
        }
    }
})


module.exports = app;