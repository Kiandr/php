<template>
  <div v-cloak>
    <div class="action-plan-state" :class="actionPlansClassObject">
      <div  v-if="action_plans_overdue.length">
        <div class="state__title text--strong">Running Late!</div>
        <div class="state__tasks text--small">{{ action_plans_overdue.length }} overdue</div>
      </div>

      <div  v-else-if="action_plans_coming_up.length">
        <div class="state__title text--strong">1 Hour!</div>
        <div class="state__tasks text--small">{{ action_plans_coming_up.length }} upcoming</div>
      </div>

      <div class="state__text" v-else>
        <div class="state__title text--strong">Nice!</div>
        <div class="state__tasks text--small">All Caught Up (for Now)</div>
      </div>

      <div class="state__thumb">
        <svg class="barry " v-bind:class="{ 'barry--uhoh': action_plans_overdue.length && (!action_plans_coming_up.length), 'barry--reminder': action_plans_coming_up.length, 'barry--coffee': action_plans.data.length && (!action_plans_coming_up.length && !action_plans_overdue.length), 'barry--tada': !action_plans.data.length && (!action_plans_coming_up.length && !action_plans_overdue.length)}" viewBox="0 0 22 252">
          <title>Barry</title>
          <path class="white" d="M191.1,252.4L191,191c0-9.3-5.2-17.9-13.5-22.2L130.2,144c-8.4-4.4-22.2-4.5-30.7-0.3l-47.6,23.7
	c-8.6,4.3-14,13-13.9,22.6l0.1,62.4H191.1z"/>
          <path class="skin" d="M171,65.8l0-1.8c-0.1-29-23.7-52.4-52.7-52.2l-7.9,0c-29,0.1-52.4,23.7-52.2,52.7l0,1.8
	c-8.3,1-14.6,7.3-14.6,14.9c0,7.6,6.5,13.9,14.9,14.8c1.5,20.2,14.4,37.1,32.2,44.5l0,4.8c0.1,13.3,10.9,24.1,24.2,24
	c13.3-0.1,24.1-10.9,24-24.2l0-4.7c17.9-7.5,30.7-24.6,32-44.9c8.3-1,14.6-7.3,14.6-14.9C185.5,73.1,179.2,66.9,171,65.8z"/>
          <path class="skin-shade" d="M118.6,144.5l-7.9,0c-7.2,0-14-1.4-20.3-4l0,5c0,0.7,0,1.5,0.1,2.2c6.8,6,15.5,9.3,24.6,9.3
	c8.6,0,16.8-3.1,23.4-8.5c0.1-1,0.2-2.1,0.2-3.1l0-4.9C132.5,143,125.7,144.4,118.6,144.5z"/>
          <g class="mouth-frown">
            <path class="white" d="M127,123.4c0.9,0,1.6-0.7,1.6-1.6c0-0.1,0-0.3-0.1-0.4c-2.1-7.7-10.1-12.3-17.8-10.2c-5,1.4-8.9,5.3-10.2,10.3
    	c-0.3,0.9,0.2,1.8,1.1,2c0.2,0.1,0.4,0.1,0.5,0.1L127,123.4z"/>
          </g>
          <g class="mouth-smile">
            <path class="white" d="M102.1,113c-0.9,0-1.6,0.7-1.6,1.6c0,0.1,0,0.3,0.1,0.4c2.1,7.7,10.1,12.3,17.8,10.2c5-1.4,8.9-5.3,10.2-10.3
	c0.3-0.9-0.2-1.8-1.1-2c-0.2-0.1-0.4-0.1-0.5-0.1L102.1,113z"/>
          </g>
          <g class="eyes">
            <ellipse transform="matrix(1 -4.014247e-03 4.014247e-03 1 -0.3248 0.3623)" class="black" cx="90.1" cy="81.1" rx="3.8" ry="4"/>
            <ellipse transform="matrix(1 -4.014247e-03 4.014247e-03 1 -0.3236 0.5553)" class="black" cx="138.2" cy="80.9" rx="3.8" ry="4"/>
          </g>
          <g class="eyebrows">
            <g class="eyebrow-left">
              <path class="brown" d="M101.1,55.2c-0.4-0.6-1-1.1-1.7-1.3c-2.4-0.5-4.8-0.6-7.2-0.2c-5.4,0.9-10.6,2.6-15.5,5.1
		c-1.4,0.7-2,2.3-1.3,3.7c0.7,1.4,2.3,2,3.7,1.3c0.1,0,0.2-0.1,0.3-0.1c4.3-2.2,8.9-3.7,13.6-4.5c1.6-0.3,3.3-0.3,4.9,0.1
		c1.5,0.4,3-0.5,3.4-2C101.5,56.5,101.4,55.8,101.1,55.2z"/>
            </g>
            <g class="eyebrow-right">
              <path class="brown" d="M127.1,55.1c0.4-0.6,1-1.1,1.7-1.3c2.4-0.6,4.8-0.6,7.2-0.2c5.4,0.8,10.6,2.5,15.5,5c1.4,0.6,2,2.3,1.4,3.7
		c-0.6,1.4-2.3,2-3.7,1.4c-0.1,0-0.2-0.1-0.3-0.2c-4.3-2.2-8.9-3.7-13.6-4.4c-1.6-0.3-3.3-0.2-4.9,0.1c-1.5,0.4-3-0.4-3.5-1.9
		C126.6,56.5,126.7,55.7,127.1,55.1z"/>
            </g>
          </g>
          <path class="skin-shade" d="M110.5,81.2c0-2,1.6-3.6,3.7-3.6l1,0c2,0,3.6,1.6,3.6,3.7l0,11.8h4.3c2,0,3.6,1.6,3.6,3.6v1
	c0,2-1.6,3.6-3.6,3.6l-9,0c-2,0-3.6-1.6-3.6-3.7L110.5,81.2z"/>
          <g>
            <ellipse transform="matrix(1 -4.014247e-03 4.014247e-03 1 -0.404 0.3284)" class="skin-shade" cx="81.6" cy="100.8" rx="6.8" ry="6.9"/>
            <ellipse transform="matrix(1 -4.014247e-03 4.014247e-03 1 -0.4034 0.593)" class="skin-shade" cx="147.5" cy="100.8" rx="6.9" ry="6.9"/>
          </g>
          <path class="brown" d="M176.6,30.5c-12.9-18-33.5-29.5-56.7-29.4C99.9,1.2,81,10,68,25.1c-0.4,0.4-0.9,0.7-1.3,1.2
	c-10.7,10.8-9.8,27.4-9.8,29.4L57,85.6c0,2,1.7,3.7,3.7,3.7h1.3c2,0,3.7-1.7,3.7-3.7l-0.1-29.9c0-0.2,0-0.5-0.1-0.7
	c5.3,2,12.2,1.3,18.1-2.6c2.8-1.8,5.1-4.1,6.8-6.7c10.1,5.8,21.8,9.1,34.2,9c6.7,0,13.4-1.1,19.7-3c0.2,0.1,0.4,0.3,0.6,0.4
	c6,3.8,12.9,4.5,18.1,2.4c0,0.2-0.1,0.5-0.1,0.7l0.1,29.9c0,2,1.7,3.7,3.7,3.6h1.3c2,0,3.6-1.7,3.6-3.7l-0.1-29.9
	c0-1.3,0.3-9-2.5-17.1C171.7,35.8,174.2,33.3,176.6,30.5z"/>
          <path class="black" d="M162.6,67.7h-4.8l-86.7,0.3h-4.8c-1.1,0-2.1,0.9-2.1,2.1c0,1.1,0.9,2.1,2.1,2.1c0,0,0,0,0,0h4.8v11.7
	c0,6.1,4.9,11,11,11l14.3-0.1c6.1,0,11-4.9,11-11l0,0V72.1l14-0.1v11.7c0,6.1,4.9,11,11,11l14.3-0.1c6.1,0,11-4.9,11-11V72h4.8
	c1.2,0,2.1-1,2.1-2.1C164.8,68.7,163.8,67.7,162.6,67.7z M105.6,83.7c0,5-4,9-9,9l-14.3,0.1c-5,0-9-4-9-9V72.1l32.3-0.1V83.7z
	 M156,83.5c0,5-4,9-9,9l-14.3,0.1c-5,0-9-4-9-9V71.9l32.3-0.1V83.5z"/>
          <path class="black" d="M141.4,173.5c0-0.2,0-0.4-0.1-0.6c-0.4-1.1-1.6-1.8-2.7-1.4l-16.4,5.2c-0.7-0.7-1.7-1.2-2.8-1.2l-7.8,0
	c-1.3,0-2.4,0.6-3.1,1.6l-16.1-5.6c-0.2-0.1-0.4-0.1-0.6-0.1c-1.2,0-2.2,0.9-2.3,2.1l0.1,18.1c0,0.2,0,0.4,0.1,0.6
	c0.4,1.1,1.6,1.8,2.7,1.4l16.7-5.3c0.7,0.6,1.6,1,2.6,1l7.8,0c1.2,0,2.2-0.5,2.9-1.3l16.2,5.6c0.2,0.1,0.4,0.1,0.6,0.1
	c1.2,0,2.2-0.9,2.3-2.1L141.4,173.5z"/>
          <g class="state-tada-hand">
            <polygon class="yellow star star1" points="21,169.9 27.3,181.9 40.6,184.2 31.1,193.8 33.2,207.1 21,201 8.9,207.1 11,193.8 1.4,184.2
		14.7,181.9 	"/>
            <path class="skin" d="M68.9,204c0.5-0.9,1.2-2.1,1.2-3.3v-3.3c0-4.2-4-8.2-8.2-8.2H42.5c0,0,4.4-22.9,4.7-27.1
		c0.5-8.2-3.5-11.2-7.7-11.2c-1.9,0-7.7,0-7.7,0s-0.2,5.8-0.2,8.2c0,0-0.7,13.3-2.6,19.1c-1.9,5.8-5.1,13.3-8.2,17.5
		c-3.3,4-11.7,14.5-11.7,24.3c0,19,6.5,25.9,30.1,25.9h20.1c3,0,5.6-1.9,5.6-5.1v-2.3c0-0.9,0-1.6-0.2-2.3c3-1.2,5.1-4,5.1-7.2v-3.3
		c0-1.9-1.2-3.7-2.3-4.9c2.6-1.2,4.2-4,4.2-7v-3.3C71.9,207.8,71,205.4,68.9,204"/>
            <polygon class="yellow star star2" points="71.5,204.5 76.6,214.3 87.6,216.2 79.9,224.1 81.5,235.1 71.5,230.2 61.6,235.1 63,224.1 55.3,216.2
		66.3,214.3 	"/>
            <polygon class="yellow star star3" points="29.9,207.3 32,211.3 36.4,212 33.2,215.2 33.9,219.7 29.9,217.8 25.7,219.7 26.4,215.2 23.1,212
		27.8,211.3 	"/>
            <polygon class="yellow star star4" points="17.5,230.2 21,236.9 28.5,238.3 23.1,243.7 24.3,251.4 17.5,247.9 10.5,251.4 11.7,243.7 6.3,238.3
		13.8,236.9 	"/>
            <polygon class="yellow star star5" points="58.8,167.8 61.4,173 67,173.9 63,177.9 64,183.5 58.8,180.9 53.7,183.5 54.6,177.9 50.7,173.9
		56.3,173 	"/>
          </g>
          <g class="state-coffee-hand">
            <g>
              <path id="Fill-43" class="orange" d="M75.7,222c0,6.5-5.3,11.7-11.7,11.7h-1.8v-24.9H64c6.5,0,11.7,5.3,11.7,11.7V222z M64,201.9
			h-1.8v-9.1c0-2.2-1.8-3.9-3.9-3.9H15.9c-2.2,0-3.9,1.8-3.9,3.9v55.9c0,2.2,1.8,3.9,3.9,3.9h42.4c2.2,0,3.9-1.8,3.9-3.9v-8.1H64
			c10.3,0,18.7-8.4,18.7-18.7v-1.4C82.7,210.3,74.3,201.9,64,201.9z"/>
              <path class="skin" d="M18.6,228.7c0-2.5-1.3-4.7-3.3-6c2-1.3,3.3-3.5,3.3-6c0-2.7-1.5-5-3.7-6.2c1.7-1.1,2.7-3,2.7-5.1
			c0-3.4-2.8-6.2-6.2-6.2s-6.2,2.8-6.2,6.2c0,2.1,1.1,4,2.7,5.1c-2.2,1.2-3.7,3.5-3.7,6.2c0,2.5,1.3,4.7,3.3,6c-2,1.3-3.3,3.5-3.3,6
			c0,2.8,1.6,5.2,4,6.4c-1.7,1-2.8,2.9-2.8,5c0,3.2,2.6,5.9,5.9,5.9s5.9-2.6,5.9-5.9c0-2.1-1.1-3.9-2.8-5
			C17,233.9,18.6,231.5,18.6,228.7z"/>
              <path id="Fill-53" class="skin" d="M69,216.2c0,3.7-3,6.7-6.7,6.7c-3.7,0-6.7-3-6.7-6.7s3-6.7,6.7-6.7
			C66.1,209.5,69,212.5,69,216.2"/>
            </g>
            <path id="Fill-55" class="white cof1" d="M40,148.2c-3.9,4.2-3.8,11.5,1.3,17.1c5,5.6-5.8,14.6-5.8,14.6c4.9-0.7,16.1-7.5,13.9-16.1
		C47.1,155.2,41.7,157.3,40,148.2"/>
            <path id="Fill-57" class="white cof2" d="M31.9,159.8c-5.9,5.9-3.9,11.3-0.2,11.6C35.4,171.8,36.2,164.3,31.9,159.8"/>
          </g>
          <g class="state-reminder-hand">
            <path class="skin" d="M57.4,204v-48.8c0-3.9-3.2-7.1-7.1-7.1h-0.1c-3.9,0-7.1,3.2-7.1,7.1v34.3c-1.6-0.3-3.3-0.4-5-0.4
		c-17.5,0-31.6,14.2-31.6,31.6s14.2,31.6,31.6,31.6c17.5,0,31.6-14.2,31.6-31.6C69.7,210.6,65.5,204,57.4,204z"/>
            <path class="white" d="M57.1,163.4c-5.1,0.2-10.1,0-15.1-0.5c-0.1,0-0.1,0-0.2,0c-0.1,0-0.1,0-0.2-0.1c-4.9-2.3-10.8-6.1-16.4-5.5
		c-3.5,0.4-6.9,3.9-5,7.5c1.6,3,6.2,4.3,11,4.3c-1.7,1.4-3.2,2.8-4.4,4.5c-2.7,3.9-0.8,8.3,4.2,8.6c4.6,0.2,13.1-9.3,13.9-15.2
		c4.1,0.3,8.3,0.4,12.4,0.3C59.6,167.3,59.6,163.4,57.1,163.4z M25.3,164.1c-1.4-0.6-2.8-1.5-0.6-2.7c1.2-0.7,3.8,0.3,5.1,0.7
		c2.3,0.7,4.5,1.7,6.6,2.8C32.7,165.4,27.5,165,25.3,164.1z M38.1,172.3c-1.2,2-4.5,7-7.5,6c-3.7-1.3,5.2-7.6,9.6-10.5
		c0,0.2-0.1,0.4-0.2,0.6C39.6,169.7,38.9,171,38.1,172.3z"/>
            <path class="skin-shade" d="M43.8,208.4c0,3.9-3.1,7-7,7h-0.4c-2.6,0-4.9-1.4-6.1-3.6c-0.1-0.1-0.2-0.1-0.2,0c-0.6,3.2-3.4,5.7-6.8,5.7
		h-0.5c-2.3,0-4.1-1.7-5.2-3.5c-0.1-0.1-0.2-0.1-0.2,0.1v0.1c0,3.1-2.5,5.6-5.6,5.6h0c-2.6,0-4.7-1.8-5.3-4.2v3.3
		c0,2.8,2.3,4.6,5,4.6h0.4c2.8,0,6-1.8,6-4.6v-0.8c1.1,2,2.5,3.1,5,3.1h0.5c3.5,0,6.4-2.2,6.8-5.6c1.1,2.3,3.5,4.4,6.2,4.4h0.5
		c3.8,0,7.1-3.8,7.1-7.6"/>
            <path id="Fill-44" class="skin" d="M36.8,183.4h-0.4c-2.7,0-5,1.6-6.1,3.8c0,0,0,0.1-0.1,0.1l-1.9,0.4c0,0-0.1,0-0.1,0
		c-1.3-1.3-3-2-4.9-2h-0.5c-3,0-5.5,1.9-6.5,4.6c0,0,0,0.1-0.1,0.1l-1.8,0.4c0,0-0.1,0-0.1,0c-0.7-0.4-1.6-0.7-2.5-0.7h-0.2
		c-2.9,0-5.3,2.4-5.3,5.3v19.1c0,3,2.4,5.4,5.4,5.4h0c3.1,0,5.6-2.5,5.6-5.6v-0.1c0-0.1,0.2-0.2,0.2-0.1c1.1,1.9,2.9,3.5,5.2,3.5
		h0.5c3.4,0,6.2-2.5,6.8-5.7c0-0.1,0.2-0.1,0.2,0c1.2,2.1,3.5,3.6,6.1,3.6h0.4c3.9,0,7-3.1,7-7v-17.9
		C43.8,186.6,40.6,183.4,36.8,183.4"/>
          </g>
          <g class="state-uhoh-hand">
            <path class="skin" d="M46.5,251.1c17.2,0,31.7-9.4,33-25.8c0.1-0.4,0.5-0.8,0.5-1.2v-27.4c0-3.7-3.8-6.3-7.5-6.3h-1.4
		c-1.8,0-3.2,0.3-4.3,1.4v-25.3c0-3.7-3.3-6.9-7-6.9h-1.4c-2.6,0-4.8,1.6-5.9,3.8v-5.7c0-3.7-3.1-6.9-6.8-6.9h-1.4
		c-3.7,0-6.1,3.2-6.1,6.9v5.7c-1.1-2.2-4.1-3.8-6.7-3.8h-1.4c-3.7,0-6.2,3.2-6.2,6.9v13.5c-1.1-1.2-2.8-1.7-4.4-1.7h-1
		c-2.7,0-4.5,1.9-4.5,4.6v36.7c0,0.3-0.2,0.6-0.1,0.8c-0.1,0.8-0.2,1.5-0.2,2.3C13.8,236.9,28.4,251.1,46.5,251.1z"/>
          </g>
        </svg>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
    :root {
        --action-plan-gutter: 1.5rem;
    }

    .action-plan-state {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        padding: var(--action-plan-gutter);
        background-color: $blue;
        position: relative;
        color: $white;

        @each $index, $color in $colors {
            &.state--#{$index} {
                background-color: $color;
            }
        }
    }

    .state__text {
        width: calc(100% - 115px);
    }

    .state__title {
        font-size: $font-size-lg;
    }

    .state__thumb {
        width: 229px;
        height: 160px;
        position: absolute;
        bottom: 0;
        right: var(--action-plan-gutter);
        pointer-events: none;

        svg {
            height: 100%;
            width: 100%;
            pointer-events: none;
        }
    }

    /* Barry Styles/Animations */
    .white {
        fill: #fff;
    }

    .skin {
        fill: #f5b6a0;
    }

    .skin-shade {
        fill: #e3998a;
    }

    .black {
        fill: #2e2e3a;
    }

    .brown {
        fill: #895a48;
    }

    .yellow {
        fill: #fcc018;
    }

    .orange {
        fill: #f79720;
    }

    .state-tada-hand,
    .state-coffee-hand,
    .state-reminder-hand,
    .state-uhoh-hand,
    .mouth-frown {
        display: none;
    }

    .barry--reminder .state-reminder-hand {
        display: block;
        animation: wag 6s linear infinite;
        transform-origin: 40px 224px !important;
    }

    .barry--tada .state-tada-hand {
        display: block;
        transform: translateY(6px);
    }

    /* animations */
    @keyframes brows {
        0% { transform: translate(0, 5px); }
        3% { transform: translate(0, 0); }
        85% { transform: translate(0, 0); }
        100% { transform: translate(0, 5px); }
    }

    @keyframes blink {
        0% { transform: scaleY(1); }
        97% { transform: scaleY(1); }
        98% { transform: scaleY(0); }
    }

    @keyframes wag {
        0% { transform: rotate(-2deg); }
        5% { transform: rotate(2deg); }
        10% { transform: rotate(-2deg); }
        15% { transform: rotate(2deg); }
        20% { transform: rotate(-2deg); }
        25% { transform: rotate(2deg); }
        30% { transform: rotate(-2deg); }
    }

    .eyes {
        animation: blink 4s ease infinite;
        transform-origin: 115px 80px !important;
    }

    @keyframes twinkle {
        0% { opacity: 0; }
        50% { opacity: 1; }
        100% { opacity: 0; }
    }

    .star1 { animation: twinkle 2s ease infinite; }
    .star2 { animation: twinkle 2s .5s ease infinite; }
    .star3 { animation: twinkle 2s 1s ease infinite; }
    .star4 { animation: twinkle 2s 1.5s ease infinite; }
    .star5 { animation: twinkle 2s 1s ease infinite; }

    .barry--uhoh .mouth-smile {
        display: none;
    }

    .barry--uhoh .mouth-frown {
        display: block;
    }

    .barry--coffee .state-coffee-hand {
        display: block;
    }

    .cof1 { animation: hideshow1 2s ease infinite; }
    .cof2 { animation: hideshow1 1s 1s ease infinite; }

    .barry--uhoh .eyebrows {
        animation: brows 12s ease infinite;
        transform-origin: 115px 60px !important;
    }

    .barry--uhoh .state-uhoh-hand {
        display: block;
    }

    @keyframes hideshow1 {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }
</style>

<script>
    /**
     * todo: fix the pluralization on the count output
     * if we move towards using vue-i18n then this is a feature out of the box.
     */


    import store from 'store';

    export default {
        computed: {
            actionPlansClassObject: function () {
                // todo: make it so the header automatically updates when your action plans status changes
                return {
                    'state--danger': this.action_plans_overdue.length,
                    'state--warning': this.action_plans_coming_up.length && !this.action_plans_overdue.length,
                    'state--primary': this.action_plans.data.length && (!this.action_plans_coming_up.length && !this.action_plans_overdue.length)
                } || 'state--primary';
            },

            action_plans: function () {
                return store.state.backend.flyouts.feeds.action_plans;
            },

            action_plans_coming_up: function () {
                return this.action_plans.coming_up || [];
            },

            action_plans_overdue: function () {
                return this.action_plans.overdue || [];
            },

            action_plans_incomplete: function () {
                return false;
            }
        }
    };
</script>