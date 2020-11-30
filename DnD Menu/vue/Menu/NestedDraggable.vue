<template>
  <draggable
    drag-class="drg"
    ghost-class="ghost"
    v-bind="dragOptions"
    class="item-container"
    tag="ul"
    :list="items"
    :group="{ name: 'g1' }"
    @change="change"
  >
    <li
      v-for="(el, index) in items"
      :key="index"
      class="item-group"
    >
      <div class="item-bar">
        <div class="item closed">
          <div class="item__caption drag-area">
            <span>{{ el.item_caption }}</span>
            <span
              style="cursor: pointer"
              @click="tagAccordion"
            >HANDLE</span>
          </div>
          <div
            draggable="false"
            class="i-collapse__content"
          >
            <tabs>
              <tab
                v-for="lang in locales"
                :key="lang.short"
                :title="lang.full"
              >
                <div class="form-group">
                  <label :for="$id(`nav_item_caption-${index}-${lang.short}`)">Caption</label>
                  <input
                    :id="$id(`nav_item_caption-${index}-${lang.short}`)"
                    v-model="el.item_caption[lang.short]"
                    class="form-control form-control-sm"
                    type="text"
                    name="nav_item_caption"
                  >
                </div>
                <div class="form-group">
                  <label :for="$id(`nav_item_description-${index}-${lang.short}`)">Description</label>
                  <input
                    :id="$id(`nav_item_description-${index}-${lang.short}`)"
                    v-model="el.item_description[lang.short]"
                    class="form-control form-control-sm"
                    type="text"
                    name="nav_item_description"
                  >
                </div>
              </tab>
            </tabs>
            <div class="form-group">
              <label :for="$id(`nav_item_url-${index}`)">Url</label>
              <input
                :id="$id(`nav_item_url-${index}`)"
                v-model="el.item_url"
                class="form-control form-control-sm"
                type="text"
                name="nav_item_url"
              >
            </div>

            <div class="form-group">
              <label :for="$id(`nav_item_icon-${index}`)">Icon</label>
              <input
                :id="$id(`nav_item_icon-${index}`)"
                v-model="el.item_icon"
                class="form-control form-control-sm"
                type="text"
                name="nav_item_icon"
              >
            </div>
            <div class="form-control form-control-sm">
              <label :for="$id(`nav_item_target-${index}`)">Target</label>
              <select
                :id="$id(`nav_item_target-${index}`)"
                v-model="el.item_target"
                name="nav_item_target"
              >
                <option
                  disabled
                  value=""
                >
                  Выберите один из вариантов
                </option>
                <option :value="null">
                  Unset
                </option>
                <option
                  v-for="(item, option_index) in target"
                  :key="'option-' + option_index"
                >
                  {{ item }}
                </option>
              </select>
            </div>

            <div>
              <button
                class="btn btn-danger"
                :disabled="debounce"
                @click="deleteItem(index)"
              >
                Remove
              </button>
            </div>
          </div>
        </div>
        <nested-draggable
          class="item-sub"
          :items="el.children"
          :target="target"
          :locales="locales"
        />
      </div>
    </li>
  </draggable>
</template>
<script>
import draggable from "vuedraggable";
import { Tabs, Tab } from 'vue-slim-tabs'

export default {
    name: "NestedDraggable",
    components: {
        draggable, Tabs, Tab,
    },
    props: {
        items: {
            type: Array,
            required: true
        },
        target: {
            type: Array,
            required: true
        },
        locales: {
            type: Array,
            required: true
        }
    },
    data: function () {
        return {
            debounce: false,
        }
    },
    computed: {
        dragOptions() {
            return {
                animation: 0,
                group: 'description',
                disabled: false,
                ghostClass: 'ghost',
                handle: '.drag-area'
            };
        },
    },
    mounted() {
    },
    methods: {
        tagAccordion(el) {
            let topParent = el.toElement.parentElement.parentElement;
            if(topParent.classList.contains('opened')) {
                topParent.classList.remove('opened')
            } else {
                topParent.classList.add('opened')
            }
        },
        change() {
            this.calculateOrder()
        },
        calculateOrder() {
            console.log('calc new order', this.items)
            this.items.forEach((e, index) => {
                console.log(e, index, e.order)
                e.order = index;
            })
            return true;
        },
        deleteItem(index) {
            if(this.debounce)
                return
            this.debounce = true;
            // eslint-disable-next-line
            this.items.splice(index, 1)
            this.debounce = !this.calculateOrder();
        },
    }
};
</script>
<style scoped lang="scss">
ul {
    list-style: none;
}
.ghost {
    //opacity: 0.5;
    background: #49bbf5;
}
.drg {
    background: red;
}

.dragArea {
    min-height: 50px;
    outline: 1px dashed;
}
.item-container {
    //max-width: 20rem;
    margin: 0;
}
.item-bar {
    clear: both;
    line-height: 1.5;
    position: relative;
    margin: 9px 0 0;
}
.item {
    position: relative;
    display: flex;
    flex-direction: column;
    font-size: 13px;
    margin-bottom: -1px;
    //cursor: move;
    width: 402px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    &__caption {
        padding: .45rem 1.25rem;
        background-color: rgb(0 0 0 / 0.02);
        border: 1px solid rgba(0,0,0,.125);
        display: flex;
        justify-content: space-between;
    }
}
.item-sub {
    //margin: 0 0 0 1rem;
    margin: 0.5em 0 0 1rem;
}

.i-collapse {
    &__content {
        //width: 402px;
        padding: 10px 0 10px 10px;
        position: relative;
        z-index: 10;
        border: 1px solid #ccd0d4;
        border-top: none;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        display: none;

        .opened & {
            display: block !important;
        }
    }
}


</style>
