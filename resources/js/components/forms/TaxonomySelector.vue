<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }}</label>
        <div class="mt-1">
            <multiselect
                v-model="selectedOptions"
                :options="options"
                :multiple="multiple"
                :taggable="true"
                @tag="addTag"
                tag-placeholder="Press enter to add"
                :placeholder="placeholder"
                label="name"
                track-by="name"
                :preserve-search="true"
                :show-labels="false"
                :searchable="true"
                :allow-empty="!isRequired"
                class="multiselect-custom"
                :class="{ 'error-border': isRequired && (!selectedOptions || (Array.isArray(selectedOptions) && selectedOptions.length === 0)) }"
            ></multiselect>
        </div>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ helpText }}
        </p>
        <p v-if="isRequired && (!selectedOptions || (Array.isArray(selectedOptions) && selectedOptions.length === 0))" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ requiredMessage }}
        </p>
        <!-- Hidden input field for form submission -->
        <template v-if="multiple">
            <input v-for="(option, index) in selectedOptions" :key="index" type="hidden" :name="name + '[]'" :value="option.name">
        </template>
        <template v-else>
            <input type="hidden" :name="name" :value="selectedOptions ? selectedOptions.name : ''">
        </template>
    </div>
</template>

<script>
export default {
    props: {
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        initialOptions: {
            type: Array,
            default: () => []
        },
        initialSelected: {
            type: Array,
            default: () => []
        },
        helpText: {
            type: String,
            default: ''
        },
        multiple: {
            type: Boolean,
            default: true
        },
        isRequired: {
            type: Boolean,
            default: false
        },
        requiredMessage: {
            type: String,
            default: 'This field is required'
        },
        placeholder: {
            type: String,
            default: 'Select or type to add...'
        }
    },
    data() {
        return {
            options: [],
            selectedOptions: null
        }
    },
    created() {
        this.processInitialData();
    },
    mounted() {
        console.log('TaxonomySelector mounted with props:', {
            id: this.id,
            name: this.name,
            multiple: this.multiple,
            isRequired: this.isRequired,
            initialOptions: this.initialOptions,
            initialSelected: this.initialSelected,
            selectedOptions: this.selectedOptions
        });
    },
    methods: {
        processInitialData() {
            // Convert string values to objects for Multiselect
            this.options = this.initialOptions.map(option => ({ name: option }));
            
            // Process selected values
            const selected = this.initialSelected || [];
            console.log(`Processing initial selected for ${this.id}:`, selected);
            
            if (this.multiple) {
                // For multiple select, keep array of objects
                this.selectedOptions = selected.map(option => ({ name: option }));
            } else {
                // For single select, use a single object or null
                this.selectedOptions = selected.length > 0 ? { name: selected[0] } : null;
            }
            
            console.log(`Processed selectedOptions for ${this.id}:`, this.selectedOptions);
        },
        addTag(newTag) {
            console.log(`Adding new tag to ${this.id}:`, newTag);
            const tag = { name: newTag };
            this.options.push(tag);
            
            if (this.multiple) {
                if (!this.selectedOptions) {
                    this.selectedOptions = [];
                }
                this.selectedOptions.push(tag);
            } else {
                // For single select, replace the current selection
                this.selectedOptions = tag;
            }
        }
    }
}
</script>

<style>
/* Custom Vue-Multiselect styles - keep existing styles */
.multiselect-custom {
    min-height: 42px;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    background-color: #fff;
}

.dark .multiselect-custom {
    border-color: #374151;
    background-color: #111827;
    color: #e5e7eb;
}

.multiselect-custom.error-border {
    border-color: #ef4444 !important;
}

.multiselect-custom .multiselect__tags {
    border-radius: 0.375rem;
    border: none;
    padding: 8px 40px 0 8px;
    min-height: 42px;
}

.dark .multiselect-custom .multiselect__tags {
    background-color: #111827;
}

.multiselect-custom .multiselect__tag {
    background-color: #65b136;
    color: white;
    border-radius: 0.25rem;
    margin-right: 5px;
}

.multiselect-custom .multiselect__tag-icon:after {
    color: white;
}

.multiselect-custom .multiselect__tag-icon:hover {
    background-color: #529429;
}

.multiselect-custom .multiselect__input,
.multiselect-custom .multiselect__single {
    background-color: transparent;
    color: inherit;
}

.dark .multiselect-custom .multiselect__input,
.dark .multiselect-custom .multiselect__single {
    color: #e5e7eb;
}

.multiselect-custom .multiselect__input::placeholder {
    color: #9ca3af;
}

.multiselect-custom .multiselect__content-wrapper {
    border-color: #d1d5db;
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.dark .multiselect-custom .multiselect__content-wrapper {
    border-color: #374151;
    background-color: #111827;
}

.multiselect-custom .multiselect__option--highlight {
    background-color: #65b136;
    color: white;
}

.multiselect-custom .multiselect__option--selected {
    background-color: #e5e7eb;
    color: #111827;
}

.dark .multiselect-custom .multiselect__option--selected {
    background-color: #374151;
    color: #e5e7eb;
}
</style>