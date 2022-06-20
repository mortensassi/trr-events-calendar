<script setup>
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'

const calendarOptions = {
  events: [
    {
      title  : 'event1',
      start  : '2022-04-19'
    },
    {
      title  : 'event2',
      start  : '2022-04-18',
      end    : '2022-04-20'
    },
    {
      title  : 'event3',
      start  : '2022-04-17T12:30:00',
      allDay : false // will make the time show
    }
  ],
  plugins: [
      dayGridPlugin,
    interactionPlugin,
    timGridPlugin,
    listPlugin
  ],
  initialView: 'dayGridMonth',
  headerToolbar: {
    start: 'dayGridYear,listYear', // will normally be on the left. if RTL, will be on the right
    center: 'title',
    end: 'today prev,next' // will normally be on the right. if RTL, will be on the left
  }
}
</script>

<template>
  <FullCalendar :options="calendarOptions" />
</template>

<style scoped lang="scss" src="@styles/app.scss"></style>
