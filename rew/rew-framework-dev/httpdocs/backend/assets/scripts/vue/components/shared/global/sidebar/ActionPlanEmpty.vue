<template>
    <div class="action-plan-empty">
        <div class="plan__empty">
            <div class="plan__thumb">
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 334.75 129.83">
                    <title>cat</title>
                    <g class="belly">
                    <path d="M6252.31,1309c-5.62-46.73-46.6-83-96.34-83s-90.71,36.27-96.34,83h192.67Z" transform="translate(-6010.23 -1179.17)" style="fill:#f79720;"/>
                    </g>
                    <path d="M6117,1309c-0.51-28.26-22.46-51-49.48-51s-49,22.74-49.48,51h99Z" transform="translate(-6010.23 -1179.17)" style="fill:#f79720;"/>
                    <path d="M6087,1263.77c-0.71-5.55-2.49-18.63-3.48-25.82a1.93,1.93,0,0,0-3.51-.82l-15.39,22.52a1.93,1.93,0,0,0,1.26,3l18.86,3.27A1.93,1.93,0,0,0,6087,1263.77Z" transform="translate(-6010.23 -1179.17)" style="fill:#f79720;"/>
                    <g class="ear"><path d="M6023.64,1283.37c-2.91-5.29-9.45-17.48-13.14-24.36a2.18,2.18,0,0,1,2.65-3.08l27.3,9.74a2.18,2.18,0,0,1,.83,3.57l-14.17,14.6A2.17,2.17,0,0,1,6023.64,1283.37Z" transform="translate(-6010.23 -1179.17)" style="fill:#f79720;"/></g>
                    <path d="M6045.59,1283.15a4.44,4.44,0,0,1,.17,1.15,4.94,4.94,0,0,1-5.09,4.77,5.2,5.2,0,0,1-3.79-1.62l-2.39,1.18a7.81,7.81,0,0,0,6.18,3,7.52,7.52,0,0,0,7.69-7.31,6.94,6.94,0,0,0-.41-2.32Z" transform="translate(-6010.23 -1179.17)" style="fill:#f2581e;"/>
                    <g class="z z1"><path d="M6048.64,1244.83a1.5,1.5,0,0,1-1.48-1.73l1.39-8.78-4.29,3.51a1.5,1.5,0,0,1-1.9-2.32l7.33-6a1.5,1.5,0,0,1,2.43,1.4l-1.5,9.49,3.91-2.35a1.5,1.5,0,0,1,1.54,2.57l-6.67,4A1.5,1.5,0,0,1,6048.64,1244.83Z" transform="translate(-6010.23 -1179.17)" style="fill:#f2581e;"/></g>
                    <g class="z z2"><path d="M6037.64,1219.83a1.5,1.5,0,0,1-1.48-1.73l1.39-8.78-4.29,3.51a1.5,1.5,0,0,1-1.9-2.32l7.33-6a1.5,1.5,0,0,1,2.43,1.4l-1.5,9.49,3.91-2.35a1.5,1.5,0,0,1,1.54,2.57l-6.67,4A1.5,1.5,0,0,1,6037.64,1219.83Z" transform="translate(-6010.23 -1179.17)" style="fill:#f2581e;"/></g>
                    <g class="z z3"><path d="M6048.64,1194.83a1.5,1.5,0,0,1-1.48-1.73l1.39-8.78-4.29,3.51a1.5,1.5,0,0,1-1.9-2.32l7.33-6a1.5,1.5,0,0,1,2.43,1.4l-1.5,9.49,3.91-2.35a1.5,1.5,0,0,1,1.54,2.57l-6.67,4A1.5,1.5,0,0,1,6048.64,1194.83Z" transform="translate(-6010.23 -1179.17)" style="fill:#f2581e;"/></g>
                    <path d="M6070.38,1270.91a4.48,4.48,0,0,1,.38,1.78,4.94,4.94,0,0,1-5.09,4.77,5.18,5.18,0,0,1-4.23-2.13l-2.33,1.15a7.78,7.78,0,0,0,6.56,3.52,7.52,7.52,0,0,0,7.69-7.31,7,7,0,0,0-.65-2.93Z" transform="translate(-6010.23 -1179.17)" style="fill:#f2581e;"/>
                    <path d="M6053,1292.4l10.29-4.78a1.37,1.37,0,0,1,1.87,1.71l-1.35,3.69a5.46,5.46,0,0,1-7,3.25l-3.69-1.35A1.37,1.37,0,0,1,6053,1292.4Z" transform="translate(-6010.23 -1179.17)" style="fill:#f2581e;"/>
                    <path d="M6344.84,1309a13.79,13.79,0,0,0,.14-1.85V1307a14,14,0,0,0-13.85-14H6142.82a14,14,0,0,0-13.85,14v0.08a15,15,0,0,0,.14,1.92h215.72Z" transform="translate(-6010.23 -1179.17)" style="fill:#f79720;"/>
                </svg>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    :root {
        --action-plan-gutter: 1.5rem;
    }

    .action-plan-empty {
        display: flex;

        /* min height triggers IE11 bug */
        height: calc(100vh - 168px);
        align-items: flex-end;

        padding-left: var(--action-plan-gutter);
        padding-right: var(--action-plan-gutter);
    }

    .plan__empty {
        overflow: hidden;
        text-align: center;
    }

    .plan__thumb {
        display: flex;

        svg {
            width: 185px;
            height: 72px;
        }
    }

    @keyframes snooze {
        0% {
            transform: scale(1, 1);
        }
        43.33% {
            transform: scale(1.05, 1.15);
        }
        86.67% {
            transform: scale(1, 1);
        }
    }

    @keyframes zzz {
        0% {
            opacity: 0;
            transform: scale(.1);
        }

        85% {
            opacity: 1;
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes earshake {
        97% {
            transform: rotate(10deg);
        }
        97.5% {
            transform: rotate(-10deg);
        }
        97.75% {
            transform: rotate(10deg);
        }
        98% {
            transform: rotate(9deg);
        }
        98.25% {
            transform: rotate(-9deg);
        }
        98.5% {
            transform: rotate(8deg);
        }
        98.75% {
            transform: rotate(-7deg);
        }
        99% {
            transform: rotate(7deg);
        }
        99.25% {
            transform: rotate(0deg);
        }
    }

    .belly {
        transform-origin: 50% 100% !important;
        animation: snooze 6s linear infinite;
    }

    .z {
        transform-origin: 50px 65px !important;
        animation: zzz 3s linear infinite;
        opacity: 0;
    }

    .z1 {
        animation-delay: 0s;
    }

    .z2 {
        animation-delay: 1s;
    }

    .z3 {
        animation-delay: 2s;
    }

    .ear {
        transform-origin: 27px 100px !important;
        animation: earshake 20s linear infinite;
    }
</style>
