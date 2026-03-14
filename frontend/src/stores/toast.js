import { defineStore } from 'pinia'

export const useToastStore = defineStore('toast', {
  state: () => ({
    message: '',
    type: 'success',
  }),

  actions: {
    show(message, type = 'success') {
      this.message = message
      this.type = type

      setTimeout(() => {
        this.message = ''
      }, 3000)
    },
  },
})
