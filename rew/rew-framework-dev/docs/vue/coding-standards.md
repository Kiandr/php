Please understand that these are _guidelines_ and you - as the expert - must use your best judgement to determine when to build a bespoke solution to a problem at hand.

## Markup `<template>`

Templates enforce that you have one root-level element and then the rest of the structure is up to you. The perfect way to satisfy that rule is to assign a generalized class to a `<div>` tag. The purpose for this is for styles (see below) and keeping the markup as clean as possible.

Favour the use of a global vue component for style consistency and predictable element structures. For example, building a content card should look like this:
```html
<card>
  <card-title>Some Title</card-title>
  <card-section>
    <ul>
      <li></li>
      <li></li>
      <li></li>
  </ul>  
  </card-section>
</card>
```

The styles attached to that component are scoped and so their associated are not shared among other components. That said, you can import the global variable sheet and mixins to replicate a design.

## Stylesheet `<style>`

The styles tag should come with `lang="scss"` and `scoped=""` attributes. (See full template example below). This enables you to freely use SCSS and not conflict with other element or components on the page.

The benefit to this is you may use extremely generalized class names now without the headache of conflicting with other items on the page. However, naming conventions are very necessary given the level of freedom provided. Please adhere to the **BEM** standard when naming something.

- **B**lock
  - `.card`
- **E**lement
  - `.card-section`
- **M**odifier
  - `.card-section_spacious`

**Complete SCSS Example**
```scss
/**
 * Content Card
 */
.card {
  border-radius: 4px; 
  background: white;
  display: block;
  
  border: 1px solid;
  border-color: #e5e6e9 #dfe0e4 #d0d1d5 #e5e6e9;
  
  /**
   * Provide a bit of a spacer from the edges.
   * Separate element so it's an "opt-in" approach.
   * Done this way so we don't have to negative margin if it's not wanted.
   */
  &-section {
    padding: .75rem;
  
    /**
     * Large spacing option in case more breathing room is needed.
     */
    &_spacious {
      padding: 1.25rem;    
    }
  }
}
```

Take note of the comment structure, try to write down _why_ you're writing what you are, every step of the way. It takes a bit more time but increases the maintainability of the code by an exponential amount and often answers the question of "why" even if you can no longer answer it yourself.

## JavaScript `<script>`

Now we get to the bread and butter of the Vue template. We can do a lot of the important stuff here like:
- Build out the logic
- Import any required (non-global) components
- Activate and leverage the Vuex store to read or update the state of the application
- Retrieve and process data from the API
- Trigger events based on lifecycle hooks

We get to make the application feel reactive, smart, modular and powerful. In every. Single. File. How cool is that?

Let's dive into the order of functions and options for a template.

> #### Data
> - imports (above `export default`)
> - props (**object**)
> - components (**object**)
> - data (**function**) ➡ must return an object
> 
> #### Lifecycle hooks (all **function**)
> - beforeCreate
> - created
> - beforeMount
> - mounted
> - beforeUpdate
> - updated
> - activated
> - deactivated
> - beforeDestroy
> - destroyed
> - errorCaptured
> 
> #### Methods, Computed Properties & Watchers
> - methods (**object**) ➡ object of functions
> - computed (**object**) ➡ object of functions
> - watch (**object**) ➡ object of functions

```javascript
    import item from './components/Item.vue';
    
    export default {
        props: {
            'data-title': {
                default: null
            }
        },
        
        components: {
            item
        },
        
        data: function() {
            return {
                items: {
                    data: []
                }
            }
        },
        
        /**
         * Lifecycle Hooks
         */
        beforeCreate: function() {},
        created: function() {},
        beforeMount: function() {},
        mounted: function() {},
        beforeUpdate: function() {},
        updated: function() {},
        activated: function() {},
        deactivated: function() {},
        beforeDestroy: function() {},
        destroyed: function() {},
        errorCaptured: function() {},
        
        /**
         * Methods, Computed Properties & Watchers
         */
        methods: {
            getData: async function() {
                // Run the async function => await for response
                let data = await API.request(not_a_real_function).get();
                
                // Mess around with the data variable here if you need to
                
                // Store to the data function.
                // Specifically into the items.data [array]
                this.items.data = data;
                
                // Doesn't need to return anything, we updated the local data so we're done.
            }
        },
        
        computed: {
            data_count: function() {
                // Access the local data variable and count the number of items in the data [array]
                let count = this.items.data.length
                
                // Return the value so we can use this property
                // It IS a function, but will be used in the template like a property
                // IE: {{ data_count }}
                return count;
            }
        },
        
        watch: {
            // Run this function anytime the items.data [array] is updated
            items: function() {
                // Let's annoy the user
                alert('Your items were updated!');
            }
        }
    }
```

### Example
This is a demonstration of a _complete_ Vue component file, you will not necessarily need all of the items in there but it paints a picture in case you need a reference.

```vue
<template>
  <!--//-->
</template>
 
<style lang="scss" scoped="">
  //
</style>
 
<script>
    export default {
        //
    }
</script>
```