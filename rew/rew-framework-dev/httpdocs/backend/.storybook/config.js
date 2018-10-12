import Vue from 'vue';
import {configure} from '@storybook/vue';
import Icon from './src/stories/icon/index';
import Button from './src/stories/button/index';
import Card from './src/stories/card/index';
import CardSection from './src/stories/cardsection/index';
import Thumb from './src/stories/thumb/index';
import Token from './src/stories/token/index';

Vue.component('Button', Button);
Vue.component('Card', Card);
Vue.component('CardSection', CardSection);
Vue.component('Icon', Icon);
Vue.component('Thumb', Thumb);
Vue.component('Token', Token);

function loadStories() {
    require('./src/stories');
}

configure(loadStories, module);