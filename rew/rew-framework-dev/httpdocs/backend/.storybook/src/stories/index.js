import {storiesOf} from '@storybook/vue';

import styles from '../styles/index.css';
// import '../../assets/styles/app.css';
import Button from './button/index';
import Thumb from './thumb/index';
import Card from './card/index';
import CardSection from './cardsection/index';
import Icon from './icon/index';
import Token from './token/index';
import LeadResults from './leadResults/index';

/**
 * Button
 */

storiesOf('Button', module)
    .add('default', () => ({
        components: {Button},
        template: `<Button>Default</Button>`
    }))
    .add('with icon', () => ({
        components: {Button, Icon},
        template: `<Button :modifiers="['primary']">
                        <Icon name="icon--thumbs-up" :width=16 :height=16 :modifiers="['invert']" />
                        <span class="text">Accept</span>
                   </Button>`
    }))
    .add('states', () => ({
        components: {Button},
        template: `<div>
            <Button :modifiers="['primary']">Primary</Button>
            <Button :modifiers="['success']">Success</Button>
            <Button :modifiers="['warning']">Warning</Button>
            <Button :modifiers="['danger']">Danger</Button>
            <Button :modifiers="['ghost']">Ghost</Button>
        </div>`
    }))

/**
 * Card
 */

storiesOf('Card', module)
    .add('default', () => ({
        components: {Card, CardSection},
        template: `<Card>
                        <CardSection>Card Content</CardSection>
                  </Card>`
    }))

/**
 * Icon
 */

storiesOf('Icon', module)
    .add('default', () => ({
        components: {Icon},
        template: `<Icon name="icon--app-listings"
                         title="Test title"
                         description="Test description"
                         :width=32
                         :height=32 />`
    }))

/**
 * Thumb
 */

storiesOf('Thumb', module)
    .add('default', () => ({
        components: {Thumb},
        template: `<Thumb />`
    }))
    .add('sizes', () => ({
        components: {Thumb},
        template: `<div>
            <Thumb :modifiers="['xs']" />
            <Thumb :modifiers="['sm']" />
            <Thumb :modifiers="['lg']" />
        </div>`
    }))
    .add('with context', () => ({
        components: {Thumb},
        template: `<Thumb :context="['John', 'Doe']" />`
    }))
    .add('with image', () => ({
        components: {Thumb},
        template: `<Thumb src="http://dev.obie.rewdev.com/thumbs/60x60/uploads/agents/agent-4.png" alt="John Doe" />`
    }))
    .add('with score', () => ({
        components: {Thumb},
        template: `<Thumb src="http://dev.obie.rewdev.com/thumbs/60x60/uploads/agents/agent-4.png"
                          alt="John Doe"
                          :score=75
                          :context="['John', 'Doe']" />`
    }))

/**
 * Token
 */

storiesOf('Token', module)
    .add('default', () => ({
        components: {Token},
        template: `<Token label="Token Label" :modifiers="['xs']" />`
    }))
    .add('with image', () => ({
        components: {Token},
        template: `<Token label="Token Label"
                          :modifiers="['xs']"
                          src="http://dev.obie.rewdev.com/thumbs/60x60/uploads/agents/agent-4.png"
                          alt="John Doe" />`
    }))
    .add('with icon', () => ({
        components: {Token, Icon},
        template: `<Token label="Token Label"
                          :modifiers="['xs']"
                          icon="icon--app-listings" />`
    }))

storiesOf('Lead Results', module)
    .add('default', () => ({
        components: {LeadResults},
        template: `<LeadResults title="Johnny Peters"
                   :context="['Johnny', 'Peters']"
                   :score = 80
                   :totalCalls = 10
                   :totalEmails = 100
                   :totalTexts = 50
                   :totalInquiries = 80 />`
    }))