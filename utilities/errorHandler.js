const errorHandler = (status, message, res) => {
    try {
        const error = new Error(message);
        error.status = status || 500;

        res.status(error.status).json({
            success: false,
            status: error.status,
            message: error.message,
        });
    } catch (error) {
        console.log(error);
    }
};

module.exports = errorHandler;