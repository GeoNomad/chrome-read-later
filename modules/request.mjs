import '../modules_web/jquery.min.js'

export async function getTitle(url) {
  const html = await getHtml(url)
  return html.filter('title').text() || url
}

export async function getHtml(url) {
  try {
    const response = await fetch(url)
    const html = await response.text()
    return $(html)
  } catch (e) {
    return $('')
  }
}

export async function getFavIcon(url) {
  try {
    const response = await fetch(`https://favicongrabber.com/api/grab/${getDomain(url)}`)
    const data = await response.json()
    return data.icons[0].src
  } catch (e) {
    return '../images/logo-gray32x32.png'
  }
}

// https://www.google.com/search?q=test => www.google.com
function getDomain(url) {
  return new URL(url).hostname
}
