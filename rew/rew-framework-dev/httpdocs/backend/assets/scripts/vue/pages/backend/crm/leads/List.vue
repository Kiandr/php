<template>
  <div class="leads" :class="{ '-is-edit-mode' : leads.edit_mode }">
    <icon-set></icon-set>
    <flyout-filterleads :options="options" name="Filter" @filter="showLoader"></flyout-filterleads>
    <div v-if="permission('crm.can.delete.leads')">
        <delete-leads :submitHandler="deleteLeads" name="Delete" :options="options.delete" title="Delete" placeholder="Placeholder" ctaIcon="icon name" ctaText="Delete"></delete-leads>
    </div>
    <div v-if="!isLender">
        <assign-leads :submitHandler="groupAssign" name="Groups" :options="options.groups" title="Groups" placeholder="Filter Groups..." ctaIcon="icon--check" ctaText="Add to Group"></assign-leads>
    </div>
    <div v-if="options.action_plans">
        <assign-leads :submitHandler="actionAssign" name="Action Plans" :options="options.action_plans" title="Action Plans" placeholder="Filter Action Plans..." ctaIcon="icon--check" ctaText="Assign to Lead" :multiple="false"></assign-leads>
    </div>
    <div v-if="permission('crm.can.assign.leads')">
        <assign-leads :submitHandler="agentAssign" name="Agents" :options="options.agents" title="Agents" placeholder="Filter Agents..." ctaIcon="icon name" ctaText="Assign to Agent" :multiple="false"></assign-leads>
    </div>
    <div class="leads--controls">
      <div :class="{ '-is-edit-mode' : leads.edit_mode }">

        <!-- Buttons that will alter the leads displayed on the view -->
        <navigation class="leads--controls_view" :modifiers="['horizontal']">
            <navigation-item class="leads__title">Leads ({{ total_results }})</navigation-item>
            <navigation-item>
                <button :class="{'-is-active' :orderBy == 'score'}" @click.native="sortLeads('score')" is="btn" :modifiers="['ghost']" class="btn--sort">
                    <span>Score</span>
                    <icon :name="sortBy == 'DESC' ? 'icon--high-to-low' : 'icon--low-to-high'"  :width="16" :height="16" class="icon--sort" />
                </button>
            </navigation-item>
            <navigation-item>
                <button :class="{'-is-active' :orderBy == 'value'}" @click.native="sortLeads('value')" is="btn" :modifiers="['ghost']" class="btn--sort">
                    <span>Value</span>
                    <icon :name="sortBy == 'DESC' ? 'icon--high-to-low' : 'icon--low-to-high'" :width="16" :height="16" class="icon--sort" />
                </button>
            </navigation-item>
            <navigation-item>
                <button :class="{'-is-active' :orderBy == 'last_touched'}" @click.native="sortLeads('last_touched')" is="btn" :modifiers="['ghost']" class="btn--sort">
                    <span>Last Touch</span>
                    <icon :name="sortBy == 'DESC' ? 'icon--high-to-low' : 'icon--low-to-high'" :width="16" :height="16" class="icon--sort" />
                </button>
            </navigation-item>
        </navigation>

        <!-- Buttons that will assign SELECTED leads accordingly -->
        <navigation class="leads--controls_assignment" :modifiers="['horizontal']">
            <div>
                <span v-if="store_all_leads_selected" class="lead-icon_checked">
                    <span class="check__text" @click="toggleSelectAllLeads">All</span>
                    <svg class="lead-icon lead-icon_checked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 31.97 32" width="24" height="24">
                        <title>Click to Unselect</title>
                        <path fill="#0096d8" d="M422,552a15.93,15.93,0,0,1-6.11-1.2,0.94,0.94,0,1,1,.72-1.74,14,14,0,0,0,5.37,1.06,1,1,0,0,1,1,.94,0.93,0.93,0,0,1-.93.94v0h0Zm5.77-1.15a0.94,0.94,0,0,1-.36-1.81A14.08,14.08,0,0,0,432,546a0.94,0.94,0,0,1,1.33,1.33,16,16,0,0,1-5.18,3.48A0.93,0.93,0,0,1,427.74,550.86Zm-16.41-3.23a0.94,0.94,0,0,1-.66-0.27,15.94,15.94,0,0,1-3.48-5.18,0.94,0.94,0,1,1,1.74-.73A14.06,14.06,0,0,0,412,546,0.94,0.94,0,0,1,411.33,547.63Zm24.55-4.93a0.94,0.94,0,0,1-.87-1.3,14,14,0,0,0,1.06-5.39,1,1,0,0,1,.94-0.95,0.93,0.93,0,0,1,.94.93v0a15.89,15.89,0,0,1-1.21,6.11A0.94,0.94,0,0,1,435.88,542.7Zm-29-5.72a0.92,0.92,0,0,1-.94-0.92v0a15.9,15.9,0,0,1,1.19-6.08,0.94,0.94,0,0,1,1.74.72,14,14,0,0,0-1.05,5.36A1,1,0,0,1,406.89,537Zm29-5.8a0.94,0.94,0,0,1-.87-0.58,14.08,14.08,0,0,0-3.07-4.58,0.94,0.94,0,0,1,1.33-1.33,16,16,0,0,1,3.47,5.18A0.94,0.94,0,0,1,435.87,531.18Zm-24.6-4.84a0.94,0.94,0,0,1-.67-1.6,15.92,15.92,0,0,1,5.17-3.49,0.94,0.94,0,0,1,.73,1.74,14,14,0,0,0-4.56,3.08A0.94,0.94,0,0,1,411.27,526.35ZM427.71,523a0.94,0.94,0,0,1-.36-0.07,14,14,0,0,0-5.4-1.07,1,1,0,0,1-1-.94,0.92,0.92,0,0,1,.91-0.94H422a15.92,15.92,0,0,1,6.12,1.21A0.94,0.94,0,0,1,427.71,523Z" transform="translate(-405.95 -520.02)"></path>
                    </svg>
                </span>
                <span v-else class="lead-icon_unchecked">
                    <span class="check__text">All</span>
                    <svg class="lead-icon lead-icon_unchecked" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="24" height="24" @click="toggleSelectAllLeads">
                        <title>Click to Select</title>
                        <path fill="#0096d8" d="M422,552a15.93,15.93,0,0,1-6.11-1.2,0.94,0.94,0,1,1,.72-1.74,14,14,0,0,0,5.37,1.06,1,1,0,0,1,1,.94,0.93,0.93,0,0,1-.93.94v0h0Zm5.77-1.15a0.94,0.94,0,0,1-.36-1.81A14.08,14.08,0,0,0,432,546a0.94,0.94,0,0,1,1.33,1.33,16,16,0,0,1-5.18,3.48A0.93,0.93,0,0,1,427.74,550.86Zm-16.41-3.23a0.94,0.94,0,0,1-.66-0.27,15.94,15.94,0,0,1-3.48-5.18,0.94,0.94,0,1,1,1.74-.73A14.06,14.06,0,0,0,412,546,0.94,0.94,0,0,1,411.33,547.63Zm24.55-4.93a0.94,0.94,0,0,1-.87-1.3,14,14,0,0,0,1.06-5.39,1,1,0,0,1,.94-0.95,0.93,0.93,0,0,1,.94.93v0a15.89,15.89,0,0,1-1.21,6.11A0.94,0.94,0,0,1,435.88,542.7Zm-29-5.72a0.92,0.92,0,0,1-.94-0.92v0a15.9,15.9,0,0,1,1.19-6.08,0.94,0.94,0,0,1,1.74.72,14,14,0,0,0-1.05,5.36A1,1,0,0,1,406.89,537Zm29-5.8a0.94,0.94,0,0,1-.87-0.58,14.08,14.08,0,0,0-3.07-4.58,0.94,0.94,0,0,1,1.33-1.33,16,16,0,0,1,3.47,5.18A0.94,0.94,0,0,1,435.87,531.18Zm-24.6-4.84a0.94,0.94,0,0,1-.67-1.6,15.92,15.92,0,0,1,5.17-3.49,0.94,0.94,0,0,1,.73,1.74,14,14,0,0,0-4.56,3.08A0.94,0.94,0,0,1,411.27,526.35ZM427.71,523a0.94,0.94,0,0,1-.36-0.07,14,14,0,0,0-5.4-1.07,1,1,0,0,1-1-.94,0.92,0.92,0,0,1,.91-0.94H422a15.92,15.92,0,0,1,6.12,1.21A0.94,0.94,0,0,1,427.71,523Z" transform="translate(-405.95 -520.02)"></path>
                    </svg>
                </span>
            </div>
            <navigation-item class="leads__title">Leads</navigation-item>
            <navigation-item v-if="permission('crm.can.assign.leads')">
                <button is="btn" :modifiers="['ghost']" @click.native="assignLeads" :disabled="!store_leads_selected.length">
                    <icon name=icon--agent :width="16" :height="16" class="icon--agent"></icon>
                    <span>Assign</span>
                </button>
            </navigation-item>
            <navigation-item>
                <div v-if="!isLender">
                    <button is="btn" :modifiers="['ghost']" @click.native="groupLeads" :disabled="!store_leads_selected.length">
                        <icon name=icon--group :width="16" :height="16" class="icon--group"></icon>
                        <span>Group</span>
                    </button>
                </div>
            </navigation-item>
            <navigation-item class="hide--lg">
                <dropdown>
                    <div is="btn" :modifiers="['ghost']" :disabled="!store_leads_selected.length" slot="toggle">
                        <span>More</span>
                        <icon name=icon--trigger :width="16" :height="16" class="icon--trigger"></icon>
                    </div>
                    <dropdown-menu slot="menu">
                        <dropdown-item>
                            <div v-if="permission('crm.can.assign.all.action.plans') || permission('crm.can.assign.own.action.plans')">
                                <button is="btn" :modifiers="['ghost']" @click.native="assignActions" :disabled="!store_leads_selected.length || !can_assign_ap_to_all_selected_leads">
                                    <icon name=icon--actionplan :width="16" :height="16" class="icon--actionplan"></icon>
                                    <span>Action Plan</span>
                                </button>
                            </div>
                        </dropdown-item>
                        <dropdown-item>
                            <div>
                                <button is="btn" :modifiers="['ghost']" @click.native="emailLeads" :disabled="!store_leads_selected.length || !can_email_all_selected_leads">
                                    <icon name=icon--email :width="16" :height="16"></icon>
                                    <span>Email</span>
                                </button>
                            </div>
                        </dropdown-item>
                        <dropdown-item>
                            <div v-if="permission('crm.can.delete.leads')">
                                <button is="btn" :modifiers="['ghost']" @click.native="deleteLeads" :disabled="!store_leads_selected.length">
                                    <icon name=icon--trash :width="16" :height="16"></icon>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </dropdown-item>
                    </dropdown-menu>
                </dropdown>
            </navigation-item>
            <navigation-item v-if="permission('crm.can.assign.all.action.plans') || permission('crm.can.assign.own.action.plans')" class="hide--sm">
                <button is="btn" :modifiers="['ghost']" @click.native="assignActions" :disabled="!store_leads_selected.length || !can_assign_ap_to_all_selected_leads">
                    <icon name=icon--actionplan :width="16" :height="16" class="icon--actionplan"></icon>
                    <span>Action Plan</span>
                </button>
            </navigation-item>
            <navigation-item class="hide--sm">
                <button is="btn" :modifiers="['ghost']" @click.native="emailLeads" :disabled="!store_leads_selected.length || !can_email_all_selected_leads">
                    <icon name=icon--email :width="16" :height="16"></icon>
                    <span>Email</span>
                </button>
            </navigation-item>
            <navigation-item v-if="permission('crm.can.delete.leads')" class="hide--sm">
                <button is="btn" :modifiers="['ghost']" @click.native="deleteLeads" :disabled="!store_leads_selected.length">
                    <icon name=icon--trash :width="16" :height="16"></icon>
                    <span>Delete</span>
                </button>
            </navigation-item>
        </navigation>
      </div>

      <!-- justify-content: space-between - so this wrapping <div> pushes any contained elements to the far side -->
      <div class="leads--actions">
        <!--
          Custom components can't have an event listener bound to them since we replace the markup
          Easiest way around that is to just wrap an element around the item with the event attached that way.
         -->

        <a href="/backend/leads/add"
            class="btn btn--ghost leads--add"
            tabindex="0" title="Add a New Lead">
            <icon name=icon--add :width="16" :height="16"  class="icon--add-lead" title="Add a New Lead" />
            <div class="tooltip">
                <div class="caret"></div>
                <span>Add a New Lead</span>
            </div>
        </a>

        <a @click="toggleEditMode"
           @keyup.enter="toggleEditMode"
           v-if="leads.data.length"
           class="btn btn--ghost leads--edit"
           :class="{'-is-active' : leads.edit_mode}"
           tabindex="0" :title="`${!leads.edit_mode ? 'Select Leads' : 'Deselect Leads'}`">
            <icon name=icon--check_circle :width="16" :height="16"  class="icon--select" />
            <div class="tooltip">
                <div class="caret"></div>
                <span v-if="!leads.edit_mode">Action Mode</span>
                <span v-else>Cancel</span>
            </div>
        </a>
          <a v-if="permission('crm.can.export.leads') && (total_results > 0)" @click="exportToCSV"
             class="btn btn--ghost leads--export"
             tabindex="1">
             <svg class="icon icon-in"><use xlink:href="/backend/img/icos.svg#icon-in"></use></svg>
              <div class="tooltip">
                  <div class="caret"></div>
                  <span>Export Leads</span>
              </div>
          </a>
          <a @click="toggleFilterFlyout"
             class="btn btn--ghost leads--search"
             tabindex="1">
              <icon name="icon--search" :width="16" :height="16"  class="icon--search" />
              <div class="tooltip">
                  <div class="caret"></div>
                  <span>Filter Leads</span>
              </div>
          </a>
          <a @click="toggleFeedFlyout"
             class="btn btn--ghost leads--feed"
             tabindex="2">
              <icon name="icon--bell" :width="16" :height="16"  class="icon--bell" />
              <div class="tooltip">
                  <div class="caret"></div>
                  <span>What's Next</span>
              </div>
          </a>
      </div>
    </div>

    <!-- When the content is loading -->
    <blankslate v-if="leads.loader">
      <loader></loader>
      <blankslate-title>Loading</blankslate-title>
      <blankslate-text>
        <loader-text></loader-text>
      </blankslate-text>
    </blankslate>

    <!-- We have results! -->
    <div v-else-if="leads.data.length" class="leads--wrap">
      <lead v-for="lead in leads.data" :key="lead.id" :lead="lead" :settings="settings"></lead>
      <button is="btn" v-if="showMore" @click.native="loadMoreLeads()">Show More</button>
    </div>

    <!-- We have no results -->
    <blankslate v-else>
      <blankslate-title>Nothing Here</blankslate-title>
      <blankslate-text>There are no leads matching your filter.</blankslate-text>
    </blankslate>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --lead-edit-padding: 3rem;
    }

    .leads {
        position: relative;
        padding-bottom: 1.75rem;

        @media (min-width: 901px) {
            padding-right: 3.125rem;
        }
    }

    .leads--controls {
        position: relative;
        z-index: 1030;
        justify-content: space-between;
        position: relative;
        align-items: center;
        display: flex;
        padding-top: 8px;
        padding-bottom: 8px;
    }

    .leads--controls_view,
    .leads--controls_assignment {
        position: absolute;
        left: 0;
        top: 25%;
        will-change: transform, opacity;
        transition: 150ms;
    }

    .leads--controls_view {
        transform: none;
        opacity: 1;
        visibility: visible;
        align-items: center;

        .-is-edit-mode > & {
            transform: translateY(-1.5rem);
            opacity: 0;
            visibility: hidden;
        }

        @media (max-width: 900px) {
            padding-left: 16px;
        }
    }

    .leads--controls_assignment {
        transform: translateY(-1.5rem);
        padding-left: var(--lead-edit-padding);
        opacity: 0;
        visibility: hidden;

        .-is-edit-mode > & {
            transform: none;
            opacity: 1;
            visibility: visible;
        }
    }

    .leads--controls_assignment .icon {
        margin-right: 4px;
    }

    .leads--controls_assignment .icon--trigger {
        position: relative;
        right: -13px;
    }

    .leads__title {
        font-size: 17px;
        margin-right: 12px;
    }

    .btn--sort {
        white-space: nowrap;
    }

    .btn--sort.-is-active {
        background-color: #dfe0e6;
        border-color: #dfe0e6;
    }

    .btn--sort .icon--sort {
        display: none;
    }

    .btn--sort.-is-active .icon--sort {
        display: inline;
    }

    .-is-high-to-low .icon--sort {
        transform: none;
    }

    .-is-low-to-high .icon--sort {
        transform: rotate(180deg);
    }

    .leads /deep/ .blankslate .loader {
        margin-bottom: .75rem;
    }

    .leads .lead {
        margin-top: 1rem;
        will-change: transform;
        transition: transform 350ms, opacity 350ms;
        transition-timing-function: cubic-bezier(.3, 0, 0, 1.3);

        @for $i from 1 through 50 {
            &:nth-child(#{$i}) {
                &,
                /deep/ .lead--icons {
                    transition-delay: #{(60 * ($i - 1))}ms;
                }
            }
        }
    }

    .leads.-is-edit-mode /deep/ .lead-icon {
        transform: translateX(-36px);
    }

    .leads.-is-edit-mode .lead {
        transform: translate3d(var(--lead-edit-padding), 0, 0);
    }

    .icon-in {
        transform: scale(1.7);
    }

    .icon--select {
        transition: fill 0.25s;
    }

    .-is-active .icon--select {
        fill: #4ead4f;
    }

    .leads--actions .btn {
        display: inline-block;
        position: relative;
        overflow: visible;
    }

    .leads--add {
        padding: 4px;
    }

    @media (min-width: 902px) {
        .leads--actions .btn.leads--add {
            display: none;
        }
    }

    .btn .tooltip {
        border: 0;
        opacity: 0;
        visibility: hidden;
        position: absolute;
        right: 0;
        top: 120%;
        z-index: 1001;
        width: auto;
        font-size: 14px;
        font-weight: bold;
        white-space: nowrap;
        padding-left: 6px;
        padding-right: 6px;
        color: #fff;
        border-radius: 3px;
        background-color: #91909a;
        text-shadow: 0 0 1px #444141;
        text-align: center;
        transition: opacity 0.6s, visibility 0.6s;
    }

    .btn .caret {
        position: absolute;
        top: -16px;
        right: 10px;
        border: 8px solid transparent;
    }

    .btn .caret::after {
        content: "";
        display: block;
        position: absolute;
        z-index: 80;
        top: -6px;
        left: -7px;
        border: 7px solid transparent;
        border-bottom-color: #91909a;
    }

    .btn:hover .tooltip {
        opacity: 1;
        visibility: visible;
    }

    @media(max-width: 530px) {
        .navigation {
            flex-wrap: wrap;
        }
    }

    .-is-edit-mode + .leads--actions {
        position: relative;
        top: -30px;
    }

    .-is-edit-mode .leads--controls_assignment {
        top: 45%;
    }

    @media (min-width: 588px) {
        .-is-edit-mode + .leads--actions {
            position: static;
        }

        .-is-edit-mode .leads--controls_assignment {
            top: 25%;
        }
    }

    @media (min-width: 901px) and (max-width: 995px) {
        .-is-edit-mode + .leads--actions {
            position: relative;
            top: -30px;
        }

        .-is-edit-mode .leads--controls_view,
        .-is-edit-mode .leads--controls_assignment {
            top: 45%;
        }
    }

    @media (min-width: 681px) and (max-width: 800px) {
        .-is-edit-mode + .leads--actions {
            position: relative;
            top: -30px;
        }

        .-is-edit-mode .leads--controls_view,
        .-is-edit-mode .leads--controls_assignment {
            top: 45%;
        }
    }

    @media(max-width: 480px) {
        .leads--actions {
            position: relative;
            top: -30px;
        }

        .leads--controls_view,
        .leads--controls_assignment {
            top: 45%;
        }
    }

    @media (min-width: 992px) {
        .flyout-open .-is-edit-mode + .leads--actions {
            position: relative;
            top: -30px;
            right: -50px;
        }

        .flyout-open .-is-edit-mode .leads--controls_assignment {
            top: 50%;
        }

        .-is-edit-mode .navigation-item .btn {
            display: flex;
            align-items: center;
            white-space: nowrap;
        }
    }

    @media (min-width: 1270px) {
        .flyout-open .-is-edit-mode + .leads--actions {
            position: relative;
            top: 0;
        }

        .flyout-open .-is-edit-mode .leads--controls_assignment {
            top: 25%;
        }
    }

    .selected {
        color: #fff;
        background-color: #1a8ffc;
    }

    .lead-icon_checked,
    .lead-icon_unchecked {
        margin-top: 5px;
        cursor: pointer;
        position: absolute;
        top: -2px;
    }

    .lead-icon_checked .check__text,
    .lead-icon_unchecked .check__text {
        position: absolute;
        left: -31px;
        top: 4px;
        font-size: 11px;
        color: #0096d8;
    }

    .lead-icon_checked svg {
        border-radius: 100%;
        background-color: #2591f8;
    }

    .lead-icon_checked .check__text {
        z-index: 10;
        color: #fff;
    }

    @media (max-width: 350px) {
        .leads--controls_view .btn {
            padding: 8px;
        }
    }

    /* Resets */

    .icon {
        width: 16px !important;
        height: 16px !important;
    }

    .icon--group {
        width: 18px !important;
        height: 18px !important;
    }

    .icon--agent,
    .icon--add-lead,
    .icon--actionplan {
        transform: scale(1.65);
    }

    .icon--trigger {
        transform: scale(1.3);
    }

    .icon--search {
        transform: scale(1.7);
    }

    .icon--bell {
        transform: scale(1.65);
    }

    .leads--controls_assignment .btn:active,
    .leads--controls_assignment .btn:focus {
        color: #fff;
        background-color: $primary;

        .icon {
            fill: #fff;
        }
    }

    .btn--ghost:hover,
    .btn--ghost:focus,
    .btn--ghost.-is-active {
        background-color: #dfe0e6;
        border-color: #dfe0e6;
        outline: 0;
    }

    /deep/ .navigation-item span {
        vertical-align: middle;
    }

    /deep/ .dropdown-item .btn {
        width: 100%;
        text-align: left;
    }

    @media (max-width: 384px) {
        .leads__title {
            margin-right: 6px;
        }

        .leads--controls_assignment .btn {
            padding: 6px;
        }

        .leads--controls_assignment .icon--group {
            width: 14px !important;
            height: 14px !important;
        }

        .leads--controls_assignment .icon--agent,
        .leads--controls_assignment .icon--actionplan {
            transform: scale(1.45);
        }

        .leads--controls_assignment .icon--trigger {
            position: static;
            right: 0;
        }
    }

    @media (min-width: 681px) {
        .hide--lg {
            display: none;
        }
    }

    @media (max-width: 680px) {
        .hide--sm {
            display: none;
        }
    }

</style>

<script>

  import actions from 'vue/actions';
  import store from 'store';
  import showSuccess from 'utils/showSuccess';
  import showErrors from 'utils/showErrors';
  import FilterLeads from 'vue/components/shared/flyouts/FilterLeads.vue';
  //import SidebarAssign from 'vue/components/leads/SidebarAssign.vue';
  //import SidebarDelete from 'vue/components/leads/SidebarDelete.vue';
  //import SidebarEmail from 'vue/components/leads/SidebarEmail.vue';
  //import SidebarGroup from 'vue/components/leads/SidebarGroup.vue';

  export default {
      data: function () {
          return {
              leads: {
                  data: [],
                  edit_mode: false,
                  endpoint: '/crm/leads',
                  loader: true
              },
              settings: {
                  partners: {}
              }
          };
      },

      components: {
          'flyout-filterleads': FilterLeads
      },

      mounted: function () {
          this.getLeads();
          if (!this.isLender) {
              store.dispatch('crm/leads/getGroupOptions');
          }
          store.dispatch('crm/leads/getActionPlanOptions');
          this.getSettings();
      },

      methods: {

          assignLeads: function () {
              store.dispatch('flyouts/open', {
                  flyout: 'Agents'
              });
          },

          agentAssign: async function ({ selectedOptions }) {
              await actions.assignAgent(
                  selectedOptions[0].value,
                  this.store_leads_selected
              ).then(() => {
                  this.toggleEditMode();
                  store.dispatch('crm/leads/clearSelected');

                  var agent = store.getters['crm/leads/getAgent'](
                      selectedOptions[0].value
                  );

                  const agentName = agent.text;
                  // Show success message
                  showSuccess([`Leads have been Assigned to ${agentName}.`], undefined, {
                      close: true,
                      expires: 10000
                  });
              }).catch(error => {
                  showErrors([error.message]);
                  throw error; // Re-throw
              });

          },


          deleteLeads: function () {
              // @todo: show `SidebarDelete`
              store.dispatch('flyouts/open', {
                  flyout: 'Delete'
              });
          },

          emailLeads: function () {
              // @todo: show `SidebarEmail`
              window.location.href = '/backend/email/?leads=' + this.store_leads_selected.join(',');
          },

          groupLeads: function () {
              store.dispatch('flyouts/open', {
                  flyout: 'Groups'
              });
          },

          getLeads: function() {
              store.dispatch('crm/leads/updateLeadResults', {
                  next: null
              });
          },

          groupAssign: async function ({ selectedOptions }) {
              const groupId = selectedOptions.map((group) => group.value);
              await actions.assignGroups(
                  this.store_leads_selected,
                  groupId
              ).then(() => {
                  this.toggleEditMode();
                  store.dispatch('crm/leads/clearSelected');
                  // Show success message
                  showSuccess(['Leads have been Assigned to Groups.'], undefined, {
                      close: true,
                      expires: 10000
                  });
              }).catch(error => {
                  showErrors([error.message]);
                  throw error; // Re-throw
              });

          },

          syncLeads: function () {
              this.leads.loader = false;
              this.leads.data = this.store_leads;
          },

          toggleEditMode: function () {
              this.leads.edit_mode = !this.leads.edit_mode;
          },

          sortLeads: function (order) {
              const currentOrder = this.orderBy;
              let sort = this.sortBy;

              if(currentOrder == order) {
                  sort = sort == 'DESC' ? 'ASC' : 'DESC';
              } else {
                  sort = 'DESC';
              }

              store.dispatch('crm/leads/setSortOrder', {
                  sort,
                  order
              });

              this.getLeads();
          },


          assignActions: function () {
              store.dispatch('flyouts/open', {
                  flyout: 'Action Plans'
              });
          },

          actionAssign: async function ({ selectedOptions, lead_id }) {
              let selectedLeads = lead_id || this.store_leads_selected;

              await actions.assignAction(
                  selectedOptions[0].value,
                  selectedLeads
              ).then(() => {
                  this.toggleEditMode();
                  // Show success message
                  showSuccess(['Leads have been Assigned to the Action Plan.'], undefined, {
                      close: true,
                      expires: 10000
                  });

                  // Close Flyout
                  store.dispatch('flyouts/close', {
                      flyout: 'Action Plans'
                  });

                  // Clear selected leads
                  store.dispatch('crm/leads/clearSelected');

                  // Reload What's Next? Action Plans Sidebar
                  this.$root.$emit('reload-action-plans');

                  store.dispatch('crm/leads/assignActionPlan', {
                      selectedLeads,
                      selectedOptions
                  });

              }).catch(error => {
                  showErrors([error.message]);
                  throw error;
              });
          },
          loadMoreLeads: function () {
              store.dispatch('crm/leads/updateLeadResults', {
                  next: store.state.crm.leads.next.replace('?after=', '')
              });
          },
          toggleSelectAllLeads: async function () {
              var toggleLeads = Object.values(this.store_leads);
              const selectedLeads = Object.values(this.store_leads_selected);

              if (!this.store_all_leads_selected) {
                  toggleLeads = toggleLeads.filter(lead => !selectedLeads.includes(lead.id));
              }

              toggleLeads.forEach(lead => {
                  store.dispatch('crm/leads/toggleSelected', {
                      lead_id: lead.id
                  });
              });
          },
          permission: function (type) {
              return store.getters['auth/getUser'].permissions[type];
          },
          exportToCSV: function () {
              var hiddenElement = document.createElement('a');
              hiddenElement.href = '/backend/ajax/crm/leads/export?' + window.location.search;
              hiddenElement.target = '_blank';
              hiddenElement.click();
          },
          toggleFilterFlyout: function () {
              const flyoutIsOpen = store.getters['flyouts/isOpen'] === 'Filter';
              const toggleMethod = flyoutIsOpen ? 'close' : 'open';
              store.dispatch(`flyouts/${toggleMethod}`, {
                  flyout: 'Filter'
              });
          },
          toggleFeedFlyout: function () {
              const flyoutIsOpen = store.getters['flyouts/isOpen'] === 'Feed';
              const toggleMethod = flyoutIsOpen ? 'close' : 'open';
              store.dispatch(`flyouts/${toggleMethod}`, {
                  flyout: 'Feed'
              });
          },
          getSettings: async function() {
              let settings = await actions.getSettings();
              this.settings = settings;
          },
          showLoader: function() {
              this.leads.loader = true;
          },
      },

      computed: {
          store_leads: function () {
              return store.getters['crm/leads/getResults'];
          },
          store_leads_selected: function () {
              return store.getters['crm/leads/getSelected'];
          },
          store_all_leads_selected: function () {
              return (this.store_leads_selected.length === this.store_leads.length);
          },
          store_leads_filters: function () {
              return store.getters['crm/leads/getFilters'];
          },
          orderBy: function () {
              return this.store_leads_filters.order;
          },
          sortBy: function () {
              return this.store_leads_filters.sort;
          },
          options: function () {
              let options = store.getters['crm/leads/getOptions'];
              const authUser = store.getters['auth/getUser'];
              if (
                  (authUser.auth && authUser.auth === '1')
                  || (authUser.type && authUser.type === 'Associate')
                  || (authUser.permissions && authUser.permissions['crm.can.manage.leads'] === true)
              ) {
                  options.status.push({
                      value: 'unassigned',
                      text: 'Unassigned'
                  });
              }
              return options;
          },
          showMore: function () {
              return store.state.crm.leads.next;
          },
          can_assign_ap_to_all_selected_leads: function () {
              const authUser = store.getters['auth/getUser'];
              // Agent has permission to assign action plans to all leads
              if (authUser.permissions['crm.can.assign.all.action.plans']) {
                  return true;
              }
              // Agent only has permission to assign action plans to their own leads
              if (authUser.type === 'Agent' && authUser.permissions['crm.can.assign.own.action.plans']) {
                  const filteredLeads = store.getters['crm/leads/getResults'],
                      selectedLeads = store.getters['crm/leads/getSelected'];
                  for (let i = 0; i < filteredLeads.length; i++) {
                      // Lead is selected but isn't assigned to the auth user
                      if (parseInt(filteredLeads[i].agent.id) !== parseInt(authUser.id) && selectedLeads.indexOf(filteredLeads[i].id) >= 0) {
                          return false;
                      }
                  }
                  return true;
              }
              return false;
          },
          can_email_all_selected_leads: function () {
              const authUser = store.getters['auth/getUser'];
              // Agent has permission to email all leads
              if (authUser.permissions['crm.can.email.all.leads']) {
                  return true;
              }
              const filteredLeads = store.getters['crm/leads/getResults'],
                  selectedLeads = store.getters['crm/leads/getSelected'];
              for (let i = 0; i < filteredLeads.length; i++) {
                  // Lead is selected but isn't assigned to the auth user
                  if (parseInt(filteredLeads[i].agent.id) !== parseInt(authUser.id) && selectedLeads.indexOf(filteredLeads[i].id) >= 0) {
                      return false;
                  }
              }
              return true;
          },
          isLender: function () {
              const authUser = store.getters['auth/getUser'];
              return authUser.type === 'Lender';
          },
          total_results: function () {
              return store.getters['crm/leads/getResultsCount'];
          }
      },
      watch: {
          store_leads: function () {
              this.syncLeads();
          }
      }
  };
</script>
