const express = require('express');
const app = express();
const Joi = require('joi');
const axios = require('axios');

const { errorHandler } = require('./utilities/errorHandler.js');

app.get('/', (req, res, next) => {
    const link = `https://adanor.eu/link`;
    console.log(`Redirect To: ${link}`);
    //res.redirect(302, link);
    res.json({
        success: false,
        status: '400',
        message: 'Home Page',
    });
})

app.get('/:id', async (req, res, next) => {
    try {
        // Validation
        const schema = Joi.object({
            id: Joi.string().pattern(/^[\w\-]{12,15}$/).required(),
        });
        await schema.validateAsync(req.params);
    } catch (error) {
        return errorHandler(400, error, next);
    }

    try {
        const id = req.params.id;
        const isProduction = process.env.NODE_ENV === 'production';
        const baseURL = isProduction ? 'https://api.adanor.eu' : 'http://localhost:8080';
        const response = await axios.get(`${baseURL}/links/${id}`);
        const data = response.data;

        if (data.blocked) {
            return errorHandler(410, 'Link is Blocked', next);
        }
        if (data.success) {
            console.log(`Redirect To: ${data.link}`);
            res.redirect(302, data.link);
        }
    } catch (error) {
        const status = error.response.status || 500;
        const message = error.response.data.message || 'API Connection Error';
        return errorHandler(status, message, next);
    }
})

// Error handling middleware
app.use(async (error, req, res, next) => {
    res.status(error.status || 500);
    await res.json({
        success: false,
        status: error.status,
        message: error.message,
    });
});

module.exports = app;