<script setup>
import { arrow, computePosition, flip, offset, shift } from '@floating-ui/dom'
import dayjs from 'dayjs'
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import { onMounted, ref } from 'vue'

const eventsData = ref(window.trr_events)
const dataLoading = ref(false)
const yearTooltipEl = ref(null)
const calendarInstance = ref(null)
const currentCalendarYear = ref()

/**
 * Reformat WP entries to get displayed in the calendar view
 * @param entry
 * @returns {{allDay: *, start: *, end: *, title: (string|*), url}}
 */
const prepareEventObject = (entry) => {
  const { start_date, end_date, one_day_event } = entry.fields
  let object = {
    title: entry.post_title,
    start: start_date,
    end: end_date,
    allDay: one_day_event,
    url: entry.url,
  }

  if (one_day_event) {
    delete object.end
  }
  return object
}

/**
 * Prevent duplicates by filtering the new data
 * @param data
 */
const prepareEventsData = (data) => {
  data.forEach((entry) => {
    if (
      !calendarOptions.value.events.some(
        (existingEvent) => existingEvent.ID === entry.ID
      )
    ) {
      calendarOptions.value.events.push(prepareEventObject(entry))
    }
  })
}

/**
 * Update calendar view according to year
 * @param year
 */
const fetchEvents = (year) => {
  const { events: initialEvents } = eventsData.value
  dataLoading.value = true

  fetch(`/wp-json/trr/v1/events/${year}`)
    .then((response) => response.json())
    .then((data) => {
      const events = data.map((entry) => {
        return prepareEventObject(entry)
      })

      const filteredEvents = events.filter(
        (event) =>
          !initialEvents.some(
            (existingEvents) => existingEvents.ID === event.ID
          )
      )

      calendarOptions.value.events = [...filteredEvents]
      dataLoading.value = false
    })
}

const calendarOptions = ref({
  events: [],
  plugins: [dayGridPlugin, interactionPlugin, timGridPlugin, listPlugin],
  initialView: 'dayGridMonth',
  customButtons: {
    yearButton: {
      text: '',
    },
  },
  headerToolbar: {
    start: 'dayGridMonth,listYear',
    center: 'title yearButton',
    end: 'today prevYear prev next nextYear',
  },
})

const setupTooltip = () => {
  const button = document.querySelector('.fc-yearButton-button')
  const tooltip = yearTooltipEl.value
  const arrowElement = document.querySelector('.tooltip__arrow')

  function update() {
    computePosition(button, tooltip, {
      placement: 'bottom',
      middleware: [
        offset(12),
        flip(),
        shift({ padding: 5 }),
        arrow({ element: arrowElement }),
      ],
    }).then(({ x, y, placement, middlewareData }) => {
      Object.assign(tooltip.style, {
        left: `${x}px`,
        top: `${y}px`,
      })

      // Accessing the data
      const { x: arrowX, y: arrowY } = middlewareData.arrow

      const staticSide = {
        top: 'bottom',
        right: 'left',
        bottom: 'top',
        left: 'right',
      }[placement.split('-')[0]]

      Object.assign(arrowElement.style, {
        left: arrowX != null ? `${arrowX}px` : '',
        top: arrowY != null ? `${arrowY}px` : '',
        right: '',
        bottom: '',
        [staticSide]: '-4px',
      })
    })
  }

  update()

  function showTooltip() {
    tooltip.classList.add('is-active')
    update()
  }

  function hideTooltip() {
    tooltip.classList.remove('is-active')
  }

  ;[
    ['click', showTooltip],
    ['focus', showTooltip],
    ['blur', hideTooltip],
  ].forEach(([event, listener]) => {
    button.addEventListener(event, listener)
  })
}

const switchYear = async (year) => {
  await fetchEvents(year)
  calendarInstance.value.calendar.gotoDate(year)
  currentCalendarYear.value = year
}

onMounted(() => {
  currentCalendarYear.value = dayjs(
    calendarInstance.value.calendar.getDate()
  ).year()
  prepareEventsData(eventsData.value.events)
  setupTooltip()
})
</script>

<template>
  <div class="loader" :class="{ 'is-active': dataLoading }">
    ...loading data
  </div>
  <FullCalendar ref="calendarInstance" :options="calendarOptions" />
  <div id="yearTooltipEl" ref="yearTooltipEl" class="tooltip" role="tooltip">
    <div class="tooltip__content">
      <nav class="nav">
        <button
          v-for="(year, yearIndex) in eventsData.years"
          :key="`events-year-option-${yearIndex}`"
          type="button"
          class="nav__item"
          :class="{ 'is-active': currentCalendarYear === year }"
          @click="switchYear(year)"
        >
          {{ year }}
        </button>
      </nav>
    </div>
    <div class="tooltip__arrow"></div>
  </div>
</template>

<style scoped lang="scss" src="@styles/app.scss"></style>
<style lang="scss">
.loader {
  transition: all 0.3s ease;
  position: fixed;
  right: 1rem;
  bottom: 1rem;
  background-color: var(--trr-theme-colors-states-info);
  color: #fff;
  padding: 0.5rem;
  z-index: 2;
  visibility: hidden;
  opacity: 0;

  &.is-active {
    visibility: visible;
    opacity: 1;
  }
}

.nav {
  display: flex;
  width: 170px;
  flex-flow: row wrap;
  margin-bottom: -0.5rem;
}

.nav__item {
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  background: transparent;
  padding: 0.5rem 0;
  box-sizing: border-box;
  flex: 0 0 calc(100% / 3 - 0.375rem);
  border: 1px solid var(--trr-theme-colors-green-800);
  border-radius: 0.25rem;
  color: var(--trr-theme-colors-green-800);
  margin: {
    right: 0.5rem;
    bottom: 0.5rem;
  }

  &:nth-child(3n + 3) {
    margin-right: 0;
  }

  &:only-child {
    margin-right: 0;
    width: 100%;
  }

  &:hover,
  &.is-active {
    background-color: var(--trr-theme-colors-green-800);
    color: #fff;
  }
}

.tooltip {
  transition: all 0.3s ease;
  z-index: 2;
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  background: #fff;
  border: 1px solid var(--trr-theme-colors-green-500);
  color: white;
  font-weight: bold;
  border-radius: 0.25rem;
  visibility: hidden;
  opacity: 0;
  transform: translateY(1rem);

  &.is-active {
    visibility: visible;
    opacity: 1;
    transform: translateY(0);
  }
}

.tooltip__content {
  position: relative;
  padding: 0.5rem;
  background: #fff;
}

.tooltip__arrow {
  position: absolute;
  background: #fff;
  width: 1rem;
  border: 1px solid var(--trr-theme-colors-green-500);
  z-index: -1;
  height: 1rem;
  transform: rotate(45deg);
}

.fc {
  position: relative;
}

.fc-toolbar-chunk {
  position: relative;
}

.fc-button {
  #trr-events-calendar-app & {
    font-size: 14px;
    background: transparent;
    border: none;
    padding: 0;
    color: var(--trr-theme-colors-green-800);
    font-weight: bold;

    &.fc-dayGridMonth-button {
      margin-right: 0.5rem;
    }

    &:hover {
      background: transparent;
    }

    &.fc-button-active {
      color: var(--trr-theme-colors-green-900);
    }

    &:first-letter {
      text-transform: uppercase;
    }
  }
}

.fc-yearButton-button {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;

  #trr-events-calendar-app & {
    margin-left: 0;
  }
}
</style>
