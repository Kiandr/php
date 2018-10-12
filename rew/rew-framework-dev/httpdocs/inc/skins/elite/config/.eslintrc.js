module.exports = {
    env: {
        browser: true,
        commonjs: true,
        es6: true,
        node: true,
        jquery: true,
    },
    globals: {
        REW: true,
        google: true,
        schools: true,
        pid: true,
        IDX: true,
        UIkit: true
    },
    extends: 'eslint:recommended',
    parserOptions: {
        sourceType: 'module',
    },
    rules: {
        'comma-dangle': ['error', 'always-multiline'],
        indent: ['error', 4],
        'linebreak-style': ['error', 'unix'],
        quotes: ['error', 'single'],
        semi: ['error', 'always'],
    },
};