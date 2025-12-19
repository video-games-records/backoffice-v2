module.exports = {
  api: {
    input: './api-schema.json',
    output: {
      target: './frontend/types/api.ts',
      client: 'fetch',
      mode: 'single', // Un seul fichier pour commencer
      clean: true
    }
  }
};