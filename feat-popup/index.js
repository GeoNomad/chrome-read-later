import '../modules/prototype.mjs'
import * as storage from '../modules-chrome/storage.mjs'
import * as tabs from '../modules-chrome/tabs.mjs'
import * as action from '../modules/domActions.mjs'
import * as dom from '../modules/domEvents.mjs'
import * as keyboard from '../modules/keyboard.mjs'
import {mouseAction} from '../modules/mouse.mjs'
import * as mouse from '../modules/mouse.mjs'
import * as readingList from '../modules/readingList.mjs'

$(async () => {
  // Remove the deleted urls from storage before init reading list.
  // Clear all the local items, includes dependingUrls, lastKey, and src.
  localStorage.getArray('dependingUrls').forEach(storage.sync.remove)
  const localStorageKeys = ['dependingUrls', 'lastKey', 'isMoving']
  localStorageKeys.forEach(key => localStorage.removeItem(key))

  await readingList.init()

  // Focus the first li on init
  const li = $('#reading-list li')
  if (li.length !== 0) li.first().addClass('active')

  // Count the reading list
  action.updateRowNumber()
  action.updateTotalCount()

  readingList.on({
    mouseenter: dom.showDeleteIcon,
    mouseleave: dom.showFavIcon
  }, 'img')

  mouse.updateStateOnMouseMove()

  readingList.on('click', event => {
    event.preventDefault()

    try {
      mouseAction(event)
    } catch (e) {
      console.log('Catch click action error: ', e)
    }
  })

  keyboard.doActionOnKeyDown()

  $('#history').on('click', async () => {
    await tabs.create(chrome.runtime.getURL('history/index.html'))
  })
})

