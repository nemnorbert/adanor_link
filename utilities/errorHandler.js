const errorHandler = (status, message, req, res) => {
    try {
        const error = new Error(message);
        error.status = status || 500;

        const isProduction = process.env.NODE_ENV === 'production';
        if (!isProduction) {
            return res.status(error.status).json({
                success: false,
                status: error.status,
                message: error.message,
            });
        }

        const siteUrl = new URL(`${req.protocol}://${req.get('host')}${req.originalUrl}`);
        let link = `https://adanor.eu/support?err=${error.status}`;
        link += `&text=${encodeURIComponent(error.message)}` || '';
        link += `&site=${encodeURIComponent(siteUrl)}` || '';

        res.redirect(302, link);

    } catch (error) {
        console.log(error);
    }
};

module.exports = errorHandler;