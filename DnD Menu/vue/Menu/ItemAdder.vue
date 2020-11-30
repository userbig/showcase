<template>
  <div class="card mb-2 p-2">
    <spinner v-if="isBusy" />
    <div>
      <div>
        <tabs>
          <template
            v-for="lang in locales"
            :slot="`vue-${lang.short}`"
          >
            <strong
              :key="'tab-' + lang.short"
              :class="{'s-error': $v.form.item_caption[lang.short].$error}"
            >
              {{ lang.full }}
            </strong>
          </template>
          <tab
            v-for="lang in locales"
            :key="lang.short"
            :title-slot="`vue-${lang.short}`"
          >
            <div class="form-group">
              <label for="nav_item_caption">Caption</label>
              <input
                id="nav_item_caption"
                v-model.trim="form.item_caption[lang.short]"
                class="form-control form-control-sm"
                type="text"
                :class="{'is-invalid': $v.form.item_caption[lang.short].$error }"
                name="nav_item_caption"
                @input="$v.form.item_caption[lang.short].$touch()"
              >
              <div
                v-if="!$v.form.item_caption[lang.short].required"
                class="text-danger"
              >
                Field is required
              </div>
              <div
                v-if="!$v.form.item_caption[lang.short].minLength"
                class="text-danger"
              >
                Name must have at least {{ $v.form.item_caption[lang.short].$params.minLength.min }} letters.
              </div>
            </div>

            <div class="form-group">
              <label for="nav_item_description">Description</label>
              <input
                id="nav_item_description"
                v-model.trim="form.item_description[lang.short]"
                class="form-control form-control-sm"
                type="text"
                name="nav_item_description"
              >
            </div>
          </tab>
        </tabs>

        <div class="form-group">
          <label for="nav_item_url">Url</label>
          <input
            id="nav_item_url"
            v-model="form.item_url"
            class="form-control form-control-sm"
            :class="{'is-invalid': $v.form.item_url.$error }"
            type="text"
            name="nav_item_url"
            @input="$v.form.item_url.$touch()"
          >
          <div
            v-if="!$v.form.item_url.required"
            class="text-danger"
          >
            Field is required
          </div>
          <div
            v-if="!$v.form.item_url.minLength"
            class="text-danger"
          >
            Name must have at least {{ $v.form.item_url.$params.minLength.min }} letters.
          </div>
        </div>
        <div class="form-group">
          <label for="nav_item_icon">Icon</label>
          <input
            id="nav_item_icon"
            v-model="form.item_icon"
            class="form-control form-control-sm"
            type="text"
            name="nav_item_icon"
          >
        </div>
        <div class="form-control form-control-sm">
          <label for="nav_item_target">Target</label>
          <select
            id="nav_item_target"
            v-model="form.item_target"
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
              v-for="(item, index) in target"
              :key="index"
            >
              {{ item }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div
      class="btn-group pt-2"
      role="group"
      aria-label="Basic example"
    >
      <button
        class="btn btn-sm btn-success"
        :disabled="$v.$invalid"
        @click="update"
      >
        Add Menu Item
      </button>
    </div>
  </div>
</template>

<script>
import { Tabs, Tab } from 'vue-slim-tabs'
import { required, minLength } from 'vuelidate/lib/validators'
import Spinner from "../misc/spinner";

export default {
    name: "ItemAdder",
    components: {
        Spinner,
      Tabs, Tab
    },
    props: {
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
            form: {
                item_caption: {},
                item_description: {},
                item_url: '',
                item_icon: null,
                item_target: null,
            },
            isBusy: false,
        }
    },
    computed: {
        rules: function() {
            let langs = this.locales.map((e) => {return e.short})
            let item_caption = {}
            langs.forEach((e) => {
                item_caption[e] = {
                    required,
                    minLength: minLength(1)
                }
            })
            return {
                form: {
                    item_caption: item_caption,
                    item_url: {
                        required,
                        minLength: minLength(1)
                    }
                }
            }
        },

    },
    validations: function() {
        return this.rules;
    },
    created() {
      this.$parent.$on('item-added', this.itemAddedHandler);
    },
    methods: {
        update() {
            this.$emit('update', this.form)
            this.isBusy = true;
        },
        itemAddedHandler() {
            this.$nextTick(() => {
                Object.assign(this.$data, this.$options.data.apply(this))
            })
        },
    }
}
</script>

<style lang="scss" src="../../scss/_tabs.scss"></style>

<style lang="scss" scoped>
.s-error {
    &:after {
        content: ' ';
        height: 5px;
        width: 5px;
        background: red;
        position: absolute;
        right: 2px;
        top: 50%;
        border-radius: 50%;
        transform: translate(0, -25%);
        animation: BLINKING-CIRCLE 1s infinite;
    }
}

@keyframes BLINKING-CIRCLE {
    0% {
        background-color: red;
    }
    50% {
        background-color: #1c2545;
    }
    0% {
        background-color: red;
    }
}
</style>
