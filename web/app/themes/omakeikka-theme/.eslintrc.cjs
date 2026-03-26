module.exports = {
  env: {
    browser: true,
    es2021: true,
  },
  extends: [
    'eslint-config-airbnb-base',
    'plugin:vue/vue3-recommended',
    'eslint:recommended',
    '@vue/typescript/recommended',
  ],
  overrides: [],
  plugins: [
    'vue',
    '@typescript-eslint',
  ],
  rules: {
    '@typescript-eslint/ban-ts-comment': 'off',
    'import/no-extraneous-dependencies': 'off',
    'vue/multi-word-component-names': 'off',
    'max-len': ['error', { code: 180 }],
    'vue/no-reserved-component-names': 'off',
    'guard-for-in': 'off',
    'no-underscore-dangle': 'off',
    'vue/no-reserved-keys': 'off',
    'no-restricted-syntax': 'off',
    'no-return-await': 'off',
    'no-bitwise': 'off',
    'no-plusplus': 'off',
    'no-continue': 'off',
    'class-methods-use-this': 'off',
    'no-shadow': 'off',
    camelcase: 'off',
    'import/prefer-default-export': 'off',
    'no-param-reassign': 'off',
    '@typescript-eslint/no-empty-function': 'off',
    'vue/no-mutating-props': 'off',
    'vue/require-valid-default-prop': 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'linebreak-style': 'off',
    'import/extensions': [
      'error',
      'ignorePackages',
      {
        js: 'off',
        jsx: 'off',
        ts: 'off',
        'd.ts': 'off',
        tsx: 'off',
      },
    ],
  },
  settings: {
    'import/resolver': {
      alias: {
        extensions: ['.js', '.jsx', '.ts', '.tsx', '.d.ts', '.vue'],
        map: [
          ['@', './resources/scripts'],
          ['@data', './resources/data'],
        ],
      },
      node: {
        extensions: ['.js', '.jsx', '.ts', '.tsx', '.d.ts', '.vue'],
        moduleDirectory: ['node_modules', './resources/scripts/'],
      },
    },
  },
};
