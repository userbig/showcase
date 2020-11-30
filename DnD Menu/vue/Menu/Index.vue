<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <MenuCreator @create="createMenu" />

        <MenuSelector
          :menu-list="menu_list"
          :selected-menu="menu.id"
          @select="selectMenu"
        />

        <ItemAdder
          v-if="menuIsNotEmpty"
          :target="target"
          :selected_menu="menu.id"
          :locales="locales"
          @update="addItem"
        />
      </div>

      <div class="col-md-6">
        <div class="card">
          <spinner v-if="isBusy" />

          <div class="card-header">
            Menu Editor
          </div>

          <div class="row">
            <div class="col ">
              <div
                class="btn-group mt-2"
                role="group"
                aria-label="Basic example"
              >
                <button
                  class="btn btn-secondary"
                  @click="updateMenu"
                >
                  Update
                </button>
                <button
                  class="btn btn-outline-success"
                  @click="fetchMenus"
                >
                  Fetch Menus
                </button>
                <button
                  class="btn btn-secondary"
                  @click="sort"
                >
                  Force Sort
                </button>
                <button
                  class="btn btn-secondary"
                  @click="addFakeItem"
                >
                  Add fake item
                </button>
                <button
                  class="btn btn-danger"
                  @click="deleteMenu"
                >
                  Delete menu
                </button>
              </div>
              <template v-if="menuIsNotEmpty">
                <nested-draggable
                  :items="menu.items"
                  :target="target"
                  :locales="locales"
                />
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import nestedDraggable from './NestedDraggable'
import axios from 'axios';
import sortBy from 'lodash/sortBy'
import ItemAdder from "./ItemAdder";
import MenuSelector from "./MenuSelector";
import MenuCreator from "./MenuCreator";
import Spinner from "../misc/spinner";

export default {
    components: {
        Spinner,
        MenuCreator,
        MenuSelector,
        ItemAdder,
        nestedDraggable,
    },
    props: {
        // eslint-disable-next-line vue/prop-name-casing
        menu_data: {
            type: Object,
            required: true
        },
        target: {
            type: Array,
            required: true
        },
        // eslint-disable-next-line vue/prop-name-casing
        menus_data: {
            type: Array,
            required: true
        },
        locales: {
            type: Array,
            required: true
        },
        // eslint-disable-next-line vue/prop-name-casing
        fallback_locale: {
            required: true,
            type: String
        }
    },
    data: function() {
        return {
            menu: this.menu_data,
            isBusy: false,
            menu_list: this.menus_data,
        }
    },

    computed: {
        menuIsNotEmpty() {
            return Object.keys(this.menu).length > 0
        }
    },
    methods: {
        async fetchMenus() {
          try {
              this.isBusy = true
              let res = await axios.get(`/admin/menu/list`)
              console.log(res.data.data)
              this.menu_list = res.data.data
          }  catch(e) {
              console.log(e)
          } finally {
              this.isBusy = false
          }
        },
        async createMenu(data) {
            try {
                this.isBusy = true
                let res = await axios.post('/admin/menu', {
                    ...data
                })
                console.log(res)
                await this.fetchMenus();
                this.$emit('menu-created')

            } catch (e) {
                console.log(e)
            } finally {
                this.isBusy = false
            }
        },
        async selectMenu(id) {
            try {
                this.isBusy = true
                let res = await axios.get(`/admin/menu/${id}`)
                this.menu = res.data.data
            } catch (e) {
                console.log(e)
            } finally {
                this.isBusy = false
            }
        },
        sort() {
            this.menu.items = this.sortItems(this.menu.items)
            console.log(this.menu.items)
        },
        sortItems(items) {
            let _this = this;
            return sortBy(items, [function(e) {
                if(e.children.length >= 1) {
                    e.children = _this.sortItems(e.children)
                }
                return e.order;
            }]);
        },
        addItem(e) {
            this.menu.items.push({
                item_caption: e.item_caption,
                item_icon: e.item_icon,
                item_description: e.item_description,
                item_id: null,
                item_menu_id: this.menu.id,
                item_parent_id: null,
                item_target: e.item_target,
                item_url: e.item_url,
                order: null,
                children: [],
            })
            this.menu.items.forEach((e, index) => {
                console.log(e, index, e.order)
                e.order = index;
            })
            this.$emit('item-added');
        },
        addFakeItem() {
            this.menu.items.push({
                item_caption: 'soqa',
                item_id: null,
                item_menu_id: this.menu.id,
                item_parent_id: null,
                item_target: null,
                item_url: 'dust',
                order: null,
                children: [],
            })
            this.menu.items.forEach((e, index) => {
                console.log(e, index, e.order)
                e.order = index;
            })
        },
        async deleteMenu() {
          if(confirm('are you sure that you want to delete this menu? this actions cannot be undone')) {
              try {
                  let res = await axios.delete(`/admin/menu/${this.menu.id}`)
                  console.log(res)
                  this.menu = [];
                  await this.fetchMenus();
              } catch (e) {
                  console.log(e)
              }
          }
        },
        async updateMenu() {
            try {
                this.isBusy = true
                let res = await axios.patch(`/admin/menu/${this.menu.id}`, {
                    'items': {...this.menu.items}
                })

                this.menu = res.data.data

            } catch (e) {
                console.log(e)
            } finally {
                this.isBusy = false
            }
        }
    }
}
</script>

<style scoped lang="scss">
</style>
