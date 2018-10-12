# Vue.js Overview

Vue is a progressive JavaScript framework with a lot of complimentary libraries that will make your time using JavaScript a _lot_ nicer to work with. This document will cover the folder structure and associated libraries to better describe what is happening under the hood.

All of the JavaScript supports ES6 and is transpiled during the build process. We also have a babel-polyfill plugin doing it's magic in the background; so a lot of the polyfill requirements are already satisfied for you.

**It is highly recommended you gear yourself up with the [Vue Browser Devtools](https://github.com/vuejs/vue-devtools) before diving into the codebase. You'll thank me later**.

### Folder Structure
Above all else, this is what you'll be faced with first, the base folder structure of the Vue implementation. Each of the folders will receive their own bullet point so we can skip those (see below), you're currently in the `README`, which then leaves the `REW.vue` file.

```
vue
├── actions
├── components
├── pages
├── router
├── store
├── utils
├── README.md
└── REW.vue
```

#### `Actions` (API)
We use [Axios](https://github.com/axios/axios) to make HTTP requests to the server. Actions is a wrapper for the axios client so that we can process error handling and filtering of the returned information. Data like the response headers are not needed past the initial reception and therefore is not passed back along to the component.

#### `Components`
The components folder is where all of the self-contained shared components live. General items like buttons, modals, content cards, dropdown menus, form controls and so-on will be sourced from here.

#### `Pages`
The router (which you will read about shortly) makes two notable decisions when you load up a URL:
  
  - It funnels everything through the `REW.vue` template allowing us to dispatch globally required API calls (like retrieving the authorized user).
  - It mounts the required page component as a child of the afforementioned `REW` component.

**The second point is where this folder comes into the picture**. The page's structural layout (flexbox, grid, etc.) will be determined by the components in here.

#### `Router`
Currently the router is _not used_. It will become more significant as we move towards a single-page application structure and begin extracting interactions away from twig and `.tpl` files.

#### `Store`
The store (Vuex) is a state management library used to effectively communicate between components, no matter how big or small the dataset.

#### `Utils`
Designated parking for extra utility files not otherwise fit to live in other folders

#### `README.md`
You are here.

#### `REW.vue`
This is the primary point of entry for the Vue router. It will dispatch the initial `action` to retrieve the authorized user information, this populates the `store` with the new information.

# Components

Components are one of the key improvements to using a JavaScript framework like Vue. We get immediate re-usability out of the globally registered [single-file components](https://vuejs.org/v2/guide/single-file-components.html).

```
vue
├── ...
├── components/
│   ├── ./
│   ├── ../
│   ├── directives.js
│   ├── filters.js
│   └── globals.js
└── ...
```

Inside of the components folder we previously talked about, we see 3 "root-level" files and then some further nesting. Let's address the files first.

#### [directives.js](./components/directives.js)
Here is the file where we can register our [custom directives](https://vuejs.org/v2/guide/custom-directive.html). The purpose of a directive is to bind a unique, custom event. You can read more about it in depth from the Vue documentation, but they should be registered globally here so that the vue instance always has access to it.

**Note that you _can_ register a directive inside of a single-file component but this is discouraged due to the nature of code isolation it creates.**

#### [filters.js](./components/filters.js)
Filters provide convenient extensions to the handlebars syntax used within templating. It's an extremely useful thing to pick up early, so [check it out on their docs](https://vuejs.org/v2/guide/filters.html). Much like directives, you may define a filter within a component, or at a higher-level at a global capacity. We of-course encourage the latter.

#### [globals.js](./components/globals.js)
Globals is the registration hub for all of our custom components. We use this to chunk out components for the purpose of [async/on-demand loading](https://vuejs.org/v2/guide/components.html#Async-Components). We do this to produce a scalable application that doesn't serve un-necessarily large files to the end-user.

An example import should look like this:
```vuejs
Vue.component('form-control', () => import('./shared/forms/Control.vue'));
```

We are:
  - Registering a vue component _globally_ (hence the file name)
  - Labelling it `form-control` so that we can later reference it in the DOM as `<form-control>`
  - Running an arrow function as the second argument for cleanliness (woo progressive!)
  - Returning back the [single-file component](https://vuejs.org/v2/guide/single-file-components.html) expected to populate this new custom HTML tag. We do this with the `import()` function because it returns a `Promise` making this all possible to begin with.

# State Management
The best thing since sliced bread. In here we've got the building blocks to the application state. We've got all the buzzwords flying around here. We will not be diving into the backend folder too deeply yet as there are some considerable changes to the structure (and subsequently this document) regarding this section, but I'll touch base on what we actually have in effect for the time being.

**This will be heavily adjusted in a rework of the store when we begin to utilize [Vuex Modules](https://vuex.vuejs.org/en/modules.html).**

```
vue
├── ...
├── store/
│   ├── backend/
│   ├── actions.js
│   ├── getters.js
│   ├── mutations.js
│   ├── state.js
│   └── store.js
└── ...
```

#### `actions.js`
We only have one for now, which is the `loadActionPlanTasks()` function. This sorts our action plans and figures out which ones are upcoming or overdue and presents us with the mapping required for the components to reflect the correct number and respond accordingly (such as the sidebar header).

#### `getters.js`
No voodoo magic going on here, just typical [Vuex Getters](https://vuex.vuejs.org/en/getters.html), they are extremely similar to ES6 getter but with the Vuex flavour.

#### `mutations.js`
Mutations should never be used outside of actions, actions are what we trigger from the code base, they hit the API and retrieve the relevant information, then _commit_ it to the store. Committing it to the store is the purpose of the mutation. Providing an explicit entryway of what it expects and the exact location it intends to modify.

Additionally, it creates a record in the [Vue-devtools](https://github.com/vuejs/vue-devtools).

#### `state.js`
The state is effectively a fancy word for a JSON Object that serves the very specific purpose of letting the Vue instance know the status of everything. This central reference structure will be changing shortly, but as it currently stands the default structure is:

```json
{
   "backend": {
      "flyouts": {
         "feeds": {
            "action_plans": {
               "coming_up": [],
               "completed": [],
               "data": [],
               "overdue": []
            },
            "mobile":{
               "open":false
            }
         }
      },
      "leads": {
         "data": [],
         "selected": []
      },
      "modal": {
         "open": false,
         "loading": false,
         "template": {
            "title": "",
            "content": ""
         },
         "modal_context": {}
      },
      "user": {
        ...
       }
   }
}
```

The scary nesting is due to a constantly changing project scope and will also be heavily updated in the movement to Vuex modules. The state imports the backend state, which them imports the flyouts, leads, modal and user objects.

#### `store.js`
It all comes together here. We import all of the previously mentioned files, instantiate the store instance, inject all of the imported files and then bind it to the vue instance. This is the centerpiece for the Vue store.

# What's Next Sidebar

In the initial release with the newly introduced Vue framework, we also have the What's Next Sidebar (or Action Plans sidebar). The entirety of this application leverages Vue capabilities.

Along with the sidebar comes the repeated action plan modules, and their functionality to trigger a modal on-demand.

The modals will trigger with one of several [modal content components](./components/shared/global/modal/actionplan). These handle additional context and logic for taking action on the various tasks. [Check out how these work](https://git.rewhosting.com/rew/rew-framework/blob/db0dac19ccf0a71df72c5a1fd9b4cef6d0f72ccd/httpdocs/backend/assets/scripts/vue/components/shared/global/sidebar/ActionPlan.vue)! 

# Libraries / Dependencies

| Library               | Version   |
|:----------------------|:---------:|
| axios                 | 0.17.1    |
| vue                   | 2.5.13    |
| vue-loader            | 13.6.0    |
| vue-moment            | 3.1.0     |
| vue-router            | 3.0.1     |
| vue-template-compiler | 2.5.13    |
| vuex                  | 3.0.1     |
