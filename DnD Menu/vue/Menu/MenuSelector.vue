<template>
  <div class="card mb-2 p-2">
    <div class="list-group">
      <button
        v-for="item in menuList"
        :key="item.id"
        type="button"
        class="list-group-item sm list-group-item-action"
        :class="{active: item.id === selectedMenu}"
        @click="selectMenu(item.id)"
      >
        {{ item.menu_name }}
      </button>
      <button
        v-if="menuList.length === 0"
        type="button"
        class="list-group-item sm list-group-item-action"
        disabled
      >
        Ни одного меню еще не создано
      </button>
    </div>
  </div>
</template>

<script>
export default {
    name: "MenuSelector",
    props: {
        menuList: {
            type: Array,
            required: true,
        },
        selectedMenu: {
            type: Number,
            default: null
        }
    },
    methods: {
        selectMenu(id) {
            if(id !== this.selectedMenu) {
                this.$emit('select', id)
            }
        }
    }
}
</script>

<style scoped lang="scss">
.sm {
    padding: 0.05rem 1.25rem;
}

</style>
