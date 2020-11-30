<template>
  <div class="card mb-2 p-2">
    <button
      class="btn btn-sm btn-secondary"
      @click="accordion = !accordion"
    >
      Create new menu
    </button>
    <div
      v-if="accordion"
      class="mt-2"
    >
      <div class="form-group">
        <label for="menu_name">Menu Name</label>
        <input
          id="menu_name"
          v-model="form.menu_name"
          class="form-control form-control-sm"
          type="text"
          name="menu_name"
        >
      </div>
      <button
        class="btn btn-sm btn-outline-success"
        :disabled="!validated"
        @click="emitter"
      >
        Create
      </button>
    </div>
  </div>
</template>

<script>
export default {
    name: "MenuCreator",
    data: function() {
        return {
            accordion: false,
            form: {
                menu_name: ''
            }
        }
    },
    computed: {
        validated() {
            return this.form.menu_name.length >= 1;
        }
    },
    created() {
        this.$parent.$on('menu-created', this.menuCreatedHandler)
    },
    methods: {
        emitter() {
          this.$emit('create', this.form);
        },
        menuCreatedHandler() {
            Object.assign(this.$data, this.$options.data())
        }
    }
}
</script>

<style scoped>

</style>
