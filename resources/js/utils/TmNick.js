export default class TmNick {
    static fontPath = 'fonts/';
    static linkProtocol = 'tmtp';
  
    static m_pattern = /\$\$|\$[0-9a-fA-F][0-9a-zA-Z]{2}|\$[twmgonishplzTWMGONISHLZ]/g;
    static m_patternLink = /(?:\[(?<url>[^\[\]]*)\]){0,1}(?<label>.+){0,1}/;
    static m_lstTags = ['h', 'p', 'l', 'o', 'g', 't', 'w', 'm', 'n', 'i', 's', 'z', 'color'];
  
    static m_css = {
      h: 'tmnick_internallink',
      p: 'tmnick_internallink_withlogin',
      l: 'tmnick_externallink',
      o: 'tmnick_bold',
      g: 'tmnick_defaultcolor',
      t: 'tmnick_upper',
      w: 'tmnick_wide',
      m: 'tmnick_normal',
      n: 'tmnick_short',
      i: 'tmnick_italic',
      s: 'tmnick_shadowed',
      z: 'tmnick_raz'
    };
  
    static m_fonts = {
      h: '',
      l: '',
      o: 'DM',
      g: '',
      t: '',
      w: '',
      m: '',
      n: 'CN',
      i: 'IT',
      s: '',
      z: 'MD'
    };
  
    static m_defaultHexaOnError = '0';
  
    static m_colorNameToHtml = {
      aliceblue: '#f0f8ff', antiquewhite: '#faebd7', aqua: '#00ffff', aquamarine: '#7fffd4', azure: '#f0ffff',
      beige: '#f5f5dc', bisque: '#ffe4c4', black: '#000000', blanchedalmond: '#ffebcd', blue: '#0000ff',
      blueviolet: '#8a2be2', brown: '#a52a2a', burlywood: '#deb887', cadetblue: '#5f9ea0', chartreuse: '#7fff00',
      chocolate: '#d2691e', coral: '#ff7f50', cornflowerblue: '#6495ed', cornsilk: '#fff8dc', crimson: '#dc143c',
      cyan: '#00ffff', darkblue: '#00008b', darkcyan: '#008b8b', darkgoldenrod: '#b8860b', darkgray: '#a9a9a9',
      darkgrey: '#a9a9a9', darkgreen: '#006400', darkkhaki: '#bdb76b', darkmagenta: '#8b008b',
      darkolivegreen: '#556b2f', darkorange: '#ff8c00', darkorchid: '#9932cc', darkred: '#8b0000',
      darksalmon: '#e9967a', darkseagreen: '#8fbc8f', darkslateblue: '#483d8b', darkslategray: '#2f4f4f',
      darkslategrey: '#2f4f4f', darkturquoise: '#00ced1', darkviolet: '#9400d3', deeppink: '#ff1493',
      deepskyblue: '#00bfff', dimgray: '#696969', dimgrey: '#696969', dodgerblue: '#1e90ff',
      firebrick: '#b22222', floralwhite: '#fffaf0', forestgreen: '#228b22', fuchsia: '#ff00ff',
      gainsboro: '#dcdcdc', ghostwhite: '#f8f8ff', gold: '#ffd700', goldenrod: '#daa520', gray: '#808080',
      grey: '#808080', green: '#008000', greenyellow: '#adff2f', honeydew: '#f0fff0', hotpink: '#ff69b4',
      indianred: '#cd5c5c', indigo: '#4b0082', ivory: '#fffff0', khaki: '#f0e68c', lavender: '#e6e6fa',
      lavenderblush: '#fff0f5', lawngreen: '#7cfc00', lemonchiffon: '#fffacd', lightblue: '#add8e6',
      lightcoral: '#f08080', lightcyan: '#e0ffff', lightgoldenrodyellow: '#fafad2', lightgray: '#d3d3d3',
      lightgrey: '#d3d3d3', lightgreen: '#90ee90', lightpink: '#ffb6c1', lightsalmon: '#ffa07a',
      lightseagreen: '#20b2aa', lightskyblue: '#87cefa', lightslategray: '#778899', lightslategrey: '#778899',
      lightsteelblue: '#b0c4de', lightyellow: '#ffffe0', lime: '#00ff00', limegreen: '#32cd32',
      linen: '#faf0e6', magenta: '#ff00ff', maroon: '#800000', mediumaquamarine: '#66cdaa',
      mediumblue: '#0000cd', mediumorchid: '#ba55d3', mediumpurple: '#9370d8', mediumseagreen: '#3cb371',
      mediumslateblue: '#7b68ee', mediumspringgreen: '#00fa9a', mediumturquoise: '#48d1cc',
      mediumvioletred: '#c71585', midnightblue: '#191970', mintcream: '#f5fffa', mistyrose: '#ffe4e1',
      moccasin: '#ffe4b5', navajowhite: '#ffdead', navy: '#000080', oldlace: '#fdf5e6', olive: '#808000',
      olivedrab: '#6b8e23', orange: '#ffa500', orangered: '#ff4500', orchid: '#da70d6',
      palegoldenrod: '#eee8aa', palegreen: '#98fb98', paleturquoise: '#afeeee', palevioletred: '#d87093',
      papayawhip: '#ffefd5', peachpuff: '#ffdab9', peru: '#cd853f', pink: '#ffc0cb', plum: '#dda0dd',
      powderblue: '#b0e0e6', purple: '#800080', red: '#ff0000', rosybrown: '#bc8f8f', royalblue: '#4169e1',
      saddlebrown: '#8b4513', salmon: '#fa8072', sandybrown: '#f4a460', seagreen: '#2e8b57',
      seashell: '#fff5ee', sienna: '#a0522d', silver: '#c0c0c0', skyblue: '#87ceeb', slateblue: '#6a5acd',
      slategray: '#708090', slategrey: '#708090', snow: '#fffafa', springgreen: '#00ff7f',
      steelblue: '#4682b4', tan: '#d2b48c', teal: '#008080', thistle: '#d8bfd8', tomato: '#ff6347',
      turquoise: '#40e0d0', violet: '#ee82ee', wheat: '#f5deb3', white: '#ffffff', whitesmoke: '#f5f5f5',
      yellow: '#ffff00', yellowgreen: '#9acd32'
    };
  
    static escapeHtml(str) {
      return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }
  
    static toHtml(
      nick,
      fontSize = 11,
      forcedShadow = false,
      forcedDefault = false,
      defaultColor = 'white',
      defaultBackgroundColor = 'transparent',
      defaultClass = 'nav-link'
    ) {
      if (forcedShadow) {
        nick = '$s' + String(nick).replace(/\$s|\$S/g, '');
      }
  
      const linkProtocol = String(this.linkProtocol).toLowerCase();
  
      let defaultHTMLColor = defaultColor;
      if (!defaultColor) {
        defaultHTMLColor = 'white';
      } else if (typeof defaultColor === 'string' && defaultColor[0] === '$') {
        defaultHTMLColor = this.colorToHtml(defaultColor);
      }
  
      let defaultHTMLBackgroundColor = defaultBackgroundColor;
      if (!defaultBackgroundColor) {
        defaultHTMLBackgroundColor = 'black';
      } else if (
        typeof defaultBackgroundColor === 'string' &&
        defaultBackgroundColor[0] === '$'
      ) {
        defaultHTMLBackgroundColor = this.colorToHtml(defaultBackgroundColor);
      }
  
      let strHtml = `<span class="tmnick_global ${defaultClass}" style="background-color:${defaultHTMLBackgroundColor}">`;
  
      let lastLink = false;
      let lastLinkTag = false;
      const stack = this._parse(nick, defaultColor);
  
      for (const ss of stack) {
        const lstClass = [];
        const lstStyle = [];
        let prefix = '';
        let suffix = '';
        const preContent = '';
        const postContent = '';
  
        for (const [tag, open] of Object.entries(ss.tagsOpen)) {
          if ((typeof open === 'string' && open !== '') || (typeof open === 'boolean' && open)) {
            if ((tag === 'h' || tag === 'p' || tag === 'l') && lastLink !== open) {
              let href = open;
              let scheme = null;
  
              try {
                scheme = new URL(href).protocol.replace(':', '');
              } catch {
                scheme = null;
              }
  
              if (scheme === null) {
                if (tag === 'l') {
                  href = 'http://' + href;
                } else if (tag === 'h') {
                  href = `${linkProtocol}:///:${href}`;
                } else if (tag === 'p') {
                  href = `${linkProtocol}:///:${href}?playerlogin=&lang=&nickname=&path=`;
                }
              }
  
              prefix += `<a href="${this.escapeHtml(href)}" target="_blank" class="${this.m_css[tag]}">`;
              lastLinkTag = tag;
              lastLink = href;
            } else if (tag === 'color') {
              if (forcedDefault) {
                lstStyle.push(`color:${defaultHTMLColor}`);
              } else {
                lstStyle.push(`color:${open}`);
              }
            } else {
              lstClass.push(this.m_css[tag]);
            }
          } else {
            if (tag === lastLinkTag && lastLink !== open) {
              prefix += '</a>';
              lastLink = false;
            }
          }
        }
  
        strHtml += prefix;
  
        if (ss.content) {
          strHtml += `<span class="${lstClass.join(' ')}" style="font-size: ${fontSize}pt;${lstStyle.join(';')}">`;
          strHtml += `${preContent}${this.escapeHtml(ss.content)}${postContent}`;
          strHtml += `</span>`;
        }
  
        strHtml += suffix;
      }
  
      const tags = ['h', 'p', 'l'];
      const last = stack.length ? stack[stack.length - 1] : null;
      for (const tag of tags) {
        if (last && last.tagsOpen[tag]) {
          strHtml += '</a>';
        }
      }
  
      strHtml += '</span>';
      return strHtml;
    }
  
    static toText(nick) {
      if (typeof nick !== 'string' || !nick) {
        return '';
      }
  
      const matches = [...nick.matchAll(new RegExp(this.m_pattern.source, 'g'))];
      let strText = '';
      let lastPos = 0;
  
      for (const match of matches) {
        const code = match[0];
        const pos = match.index;
  
        if (code !== '$$') {
          strText += nick.substring(lastPos, pos);
          lastPos = pos + code.length;
        }
      }
  
      strText += nick.substring(lastPos);
      strText = strText.replace(/(\[[^\[\]]*\])/g, '');
  
      return strText;
    }
  
    static colorToHtml(color) {
      let colorHtml = '#';
  
      if (
        typeof color === 'string' &&
        color &&
        color.length === 4 &&
        (color[0] === '$' || color[0] === '#')
      ) {
        color = color.toLowerCase().replace(/[g-z]/g, this.m_defaultHexaOnError);
        colorHtml += color[1] + color[1];
        colorHtml += color[2] + color[2];
        colorHtml += color[3] + color[3];
      } else if (Array.isArray(color) && color.length === 3) {
        colorHtml = `#${color[0].toString(16).padStart(2, '0')}${color[1]
          .toString(16)
          .padStart(2, '0')}${color[2].toString(16).padStart(2, '0')}`;
      } else if (
        typeof color === 'string' &&
        color &&
        color.length === 7 &&
        color[0] === '#'
      ) {
        color = color.toLowerCase().replace(/[g-z]/g, this.m_defaultHexaOnError);
        colorHtml = color;
      } else {
        color = String(color).toLowerCase();
        colorHtml = this.m_colorNameToHtml[color] || '#000000';
      }
  
      return colorHtml;
    }
  
    static colorToRGB(color) {
      let colorRGB = [0, 0, 0];
  
      if (
        typeof color === 'string' &&
        color &&
        color.length === 4 &&
        (color[0] === '$' || color[0] === '#')
      ) {
        color = color.toLowerCase().replace(/[g-z]/g, this.m_defaultHexaOnError);
  
        colorRGB[0] = parseInt(color[1] + color[1], 16);
        colorRGB[1] = parseInt(color[2] + color[2], 16);
        colorRGB[2] = parseInt(color[3] + color[3], 16);
      } else if (Array.isArray(color) && color.length === 3) {
        colorRGB = color;
      } else if (
        typeof color === 'string' &&
        color &&
        color.length === 7 &&
        color[0] === '#'
      ) {
        color = color.toLowerCase().replace(/[g-z]/g, this.m_defaultHexaOnError);
  
        colorRGB[0] = parseInt(color.slice(1, 3), 16);
        colorRGB[1] = parseInt(color.slice(3, 5), 16);
        colorRGB[2] = parseInt(color.slice(5, 7), 16);
      } else {
        color = String(color).toLowerCase();
        colorRGB = this.colorToRGB(this.m_colorNameToHtml[color] || '#000000');
      }
  
      return colorRGB;
    }
  
    static getCss() {
      return `<style type="text/css">
        .tmnick_global {
          font-style: normal;
          font-weight: normal;
          letter-spacing: 0;
        }
        .tmnick_normal { letter-spacing: 0; }
        .tmnick_short { letter-spacing: -1px; }
        .tmnick_wide { letter-spacing: 2px; }
        .tmnick_raz { font-style: normal; font-weight: normal; letter-spacing: 0; }
        .tmnick_italic { font-style: italic; }
        .tmnick_bold { font-weight: bold; letter-spacing: 1px; }
        .tmnick_upper { text-transform: uppercase; }
        span.tmnick_shadow { text-shadow: 0 1px 1px #333; }
        .tmnick_shadowed { position: relative; }
        .tmnick_shadowed span { position: relative; }
        span.tmnick_shadow { position: absolute; }
        .tmnick_defaultcolor { }
        a.tmnick_internallink, a.tmnick_internallink:visited {
          color: inherit;
          text-decoration: none;
          border-bottom: thin solid Black;
        }
        a.tmnick_internallink:hover {
          color: inherit;
          text-decoration: none;
          border-bottom: thin solid Blue;
        }
        a.tmnick_internallink_withlogin, a.tmnick_internallink_withlogin:visited  {
          color: inherit;
          text-decoration: none;
          border-bottom: thin solid #F0E68C;
        }
        a.tmnick_internallink_withlogin:hover {
          color: inherit;
          text-decoration: none;
          border-bottom: thin solid Gold;
        }
        a.tmnick_externallink, a.tmnick_externallink:visited {
          color: inherit;
          text-decoration: none;
          border-bottom: thin solid Black;
        }
        a.tmnick_externallink:hover {
          color: inherit;
          text-decoration: none;
          border-bottom: thin solid Orange;
        }
      </style>`;
    }
  
    static stripNadeoCode(str, select = []) {
      const stripNadeoCode = select.length
        ? select
        : ['$w', '$W', '$n', '$N', '$o', '$O', '$i', '$I', '$t', '$T', '$s', '$S', '$g', '$G', '$z', '$Z', '$<'];
  
      let out = String(str);
      for (const code of stripNadeoCode) {
        out = out.replace(new RegExp(code.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi'), '');
      }
      return out;
    }
  
    static _parse(nick, defaultColor = 'white') {
      if (typeof nick !== 'string' || !nick) {
        return [];
      }
  
      let defaultHTMLColor = defaultColor;
      if (!defaultColor) {
        defaultHTMLColor = 'white';
      } else if (typeof defaultColor === 'string' && defaultColor[0] === '$') {
        defaultHTMLColor = this.colorToHtml(defaultColor);
      }
  
      const matches = [...nick.matchAll(new RegExp(this.m_pattern.source, 'g'))];
  
      let strContent = '';
      let lastPos = 0;
  
      let tagsOpen = {
        h: false,
        p: false,
        l: false,
        o: false,
        g: false,
        t: false,
        w: false,
        m: false,
        n: false,
        i: false,
        s: false,
        color: defaultHTMLColor
      };
  
      let stack = [];
  
      for (const match of matches) {
        const code = match[0];
        const pos = match.index;
  
        let tag = code.substring(1).toLowerCase();
        let param = null;
  
        if (tag.length === 3) {
          param = tag;
          tag = 'color';
        }
  
        if (!this.m_lstTags.includes(tag)) {
          continue;
        }
  
        strContent = nick.substring(lastPos, pos);
        if (strContent) {
          stack.push({ content: strContent, tagsOpen: { ...tagsOpen } });
        }
  
        switch (tag) {
          case 'z':
            tagsOpen = {
              ...tagsOpen,
              o: false,
              g: false,
              t: false,
              w: false,
              m: false,
              n: false,
              i: false,
              s: false,
              color: defaultHTMLColor
            };
            break;
  
          case 'g':
            tagsOpen.color = defaultHTMLColor;
            break;
  
          case 'h':
          case 'p':
          case 'l':
            if (!tagsOpen[tag]) {
              tagsOpen[tag] = true;
            } else {
              let strLink = '';
              let i = stack.length - 1;
  
              while (i >= 0 && stack[i].tagsOpen[tag]) {
                strLink = stack[i].content + strLink;
                i--;
              }
  
              let url = null;
              const regs = strLink.match(this.m_patternLink);
  
              if (regs && regs.groups && regs.groups.label) {
                if (!regs.groups.url) {
                  url = regs.groups.label;
                } else {
                  url = regs.groups.url;
                }
              }
  
              let lastMax = strLink.length;
              for (let j = stack.length - 1; j >= i + 1; j--) {
                if (url !== null) {
                  const subLabel = stack[j].content;
                  const labelPos = regs ? regs.index ?? 0 : 0;
                  const max = Math.max(lastMax - subLabel.length, labelPos);
                  stack[j].content = strLink.substring(max, lastMax);
                  lastMax = max;
                  stack[j].tagsOpen[tag] = url;
                } else {
                  delete stack[j];
                }
              }
  
              stack = stack.filter(Boolean);
              tagsOpen[tag] = false;
            }
            break;
  
          case 's':
          case 't':
          case 'o':
          case 'i':
            tagsOpen[tag] = !tagsOpen[tag];
            break;
  
          case 'w':
          case 'm':
          case 'n':
            if (!tagsOpen[tag]) {
              tagsOpen.w = false;
              tagsOpen.m = false;
              tagsOpen.n = false;
              tagsOpen[tag] = true;
            } else {
              tagsOpen[tag] = false;
            }
            break;
  
          case 'color':
            tagsOpen.color = this.colorToHtml(code);
            break;
  
          default:
            if (stack.length) {
              stack[stack.length - 1].content += code;
            }
            break;
        }
  
        stack = stack.filter(st => st && st.content);
        lastPos = pos + code.length;
      }
  
      strContent = nick.substring(lastPos);
      if (strContent) {
        stack.push({ content: strContent, tagsOpen: { ...tagsOpen } });
      }
  
      for (const tag of ['h', 'p', 'l']) {
        if (tagsOpen[tag]) {
          let strLink = '';
          let i = stack.length - 1;
  
          while (i >= 0 && stack[i].tagsOpen[tag]) {
            strLink = stack[i].content + strLink;
            i--;
          }
  
          let url = null;
          const regs = strLink.match(this.m_patternLink);
  
          if (regs && regs.groups && regs.groups.label) {
            if (!regs.groups.url) {
              url = regs.groups.label;
            } else {
              url = regs.groups.url;
            }
          }
  
          let lastMax = strLink.length;
          for (let j = stack.length - 1; j >= i + 1; j--) {
            if (url !== null) {
              const subLabel = stack[j].content;
              const labelPos = regs ? regs.index ?? 0 : 0;
              const max = Math.max(lastMax - subLabel.length, labelPos);
              stack[j].content = strLink.substring(max, lastMax);
              lastMax = max;
              stack[j].tagsOpen[tag] = url;
            } else {
              delete stack[j];
            }
          }
  
          stack = stack.filter(Boolean);
          tagsOpen[tag] = false;
        }
      }
  
      return stack;
    }
  
    static toImg() {
      throw new Error(
        'toImg n’est pas implémentée dans cette version JS. En JavaScript, il faut une version spécifique navigateur (Canvas) ou Node.js (package "canvas").'
      );
    }
  }