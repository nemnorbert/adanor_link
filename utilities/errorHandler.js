function errorHandler (status, message, next) {
    const error = new Error(message);
    error.status = status || 500;
    next(error);
}

module.exports = { errorHandler };