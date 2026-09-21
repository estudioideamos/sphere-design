export function addJournal(pages, { picture, eyebrow, link, cta }) {
  const posts = [
    {
      slug: "the-first-image",
      category: "Perspective",
      title: "Before the first image, a better question.",
      image: "exterior1",
      intro:
        "A compelling visualization starts with a clear purpose. What should someone understand, notice or feel when they see this project?",
      sections: [
        [
          "Start with the decision",
          "An image can introduce a development, explain a design proposal or help a team compare alternatives. Those are different tasks. Before choosing a camera angle, define the conversation the image needs to support. A clear brief gives every later decision a point of reference.",
        ],
        [
          "Choose what deserves attention",
          "A wide view can explain the relationship between a building and its surroundings. A closer composition can reveal an entrance, a material transition or the scale of a shared space. Trying to communicate everything in one frame often makes the main idea harder to read. Give the image a focal point and let supporting details establish context.",
        ],
        [
          "Build a shared visual brief",
          "Gather the current drawings, material references and examples of the atmosphere you want to explore. Separate fixed design decisions from elements still under discussion. This makes feedback more specific: a comment about the camera is different from a change to the architecture.",
        ],
        [
          "Review the idea before the detail",
          "At an early stage, look at composition, proportion and the direction of light. Ask whether the view tells the intended story before refining accessories or surface texture. The most useful first image is one that helps the next decision become clearer.",
        ],
      ],
    },
    {
      slug: "light-and-material",
      category: "Material studies",
      title: "The quiet dialogue between light and material.",
      image: "interior1",
      intro:
        "Stone, timber and fabric do more than fill a room. Their relationship with light gives a space its rhythm, depth and character.",
      sections: [
        [
          "Read the room as a whole",
          "A material palette is experienced as a composition. Warm timber can soften a stone surface; a matte finish can give a reflective detail room to stand out. Start by looking at the balance between the large surfaces before judging an individual swatch.",
        ],
        [
          "Let light reveal the surface",
          "The same finish can appear very different under broad daylight or a small, directional source. In an image, light helps reveal texture, edges and changes in plane. Comparing a few deliberate lighting conditions can make those differences easier to discuss without changing the entire design.",
        ],
        [
          "Give details a sense of scale",
          "A texture needs to belong to the object it covers. The size of a grain, the rhythm of joints and the thickness of an edge all contribute to that reading. Close views are useful for examining these relationships, while wider views help keep the overall atmosphere in perspective.",
        ],
        [
          "Use references as a conversation",
          "Photographs and samples provide a shared starting point for color and finish, but a rendered image is still an interpretation. Screens, exposure and lighting affect what we see. Use the visualization to discuss relationships and intent, alongside physical samples when final selections matter.",
        ],
      ],
    },
    {
      slug: "beyond-the-frame",
      category: "Immersive experiences",
      title: "Beyond the frame: choosing how a space is seen.",
      image: "interior2",
      intro:
        "A still image, a film and a 360° view invite different ways of looking. The right format follows the story you want to tell.",
      sections: [
        [
          "A still image directs the eye",
          "A carefully framed view gives a project a clear visual statement. It lets the audience pause, compare and return to specific details. A small series of complementary views can introduce a space, explain its context and bring selected moments closer.",
        ],
        [
          "A film creates a sequence",
          "Movement introduces time. An approach to an entrance, a shift from an exterior to an interior or a gradual reveal of a room can establish a narrative. Planning that sequence early helps determine which spaces need to connect and where the viewer should have time to pause.",
        ],
        [
          "A panorama invites exploration",
          "A 360° view gives the viewer control over where to look from a chosen position. That freedom changes the composition: the whole surrounding scene needs attention. It is especially useful when the relationship between different sides of a room matters to the conversation.",
        ],
        [
          "Match the format to the audience",
          "Consider where the work will be viewed, how much time the audience has and what they need to understand. A short introduction and a deeper exploration can complement one another. Start with the communication goal, then choose the format that makes that goal easiest to experience.",
        ],
      ],
    },
  ];
  const card = (p, i) =>
    `<a class="journal-card" href="${p.slug}.html"><div class="journal-image">${picture(p.image, p.title)}<span aria-hidden="true">↗</span></div><div class="journal-meta"><span>${p.category}</span></div><h2>${p.title}</h2><p>${p.intro}</p><span class="journal-read">Read story <span aria-hidden="true">↗</span></span></a>`;
  pages["insights.html"] = {
    title: "Insights — The Sphere Perspective",
    active: "Insights",
    description:
      "Ideas on architectural visualization, light, materials and immersive experiences.",
    body: `<section class="page-title wrap">${eyebrow("INSIGHTS", "THE SPHERE PERSPECTIVE")}<h1>Ideas behind<br><em>the image.</em></h1><div class="title-bottom"><p>Notes on architecture, atmosphere<br>and the art of seeing what comes next.</p></div></section><section class="journal wrap" aria-label="Journal articles"><div class="journal-label"><span>THE JOURNAL</span><span>Sample editorial content</span></div><div class="journal-grid">${posts.map(card).join("")}</div></section>${cta()}`,
  };
  posts.forEach((p, i) => {
    const next = posts[(i + 1) % posts.length];
    pages[p.slug + ".html"] = {
      title: p.title,
      active: "Insights",
      description: p.intro,
      body: `<article class="journal-article"><header class="article-heading wrap">${link("insights.html", "All insights")}<p class="eyebrow">${p.category} / 2 MIN READ</p><h1>${p.title}</h1><p class="article-deck">${p.intro}</p><p class="sample-note">Sample editorial · Demo edition</p></header><figure class="article-cover wrap">${picture(p.image, p.title)}<figcaption>Sphere Design / A study in architectural visualization</figcaption></figure><div class="article-layout wrap"><aside><span class="eyebrow">IN THIS STORY</span><ol>${p.sections.map(([h], j) => `<li><a href="#section-${j + 1}">${h}</a></li>`).join("")}</ol></aside><div class="article-copy">${p.sections.map(([h, t], j) => `<section id="section-${j + 1}"><h2>${h}</h2><p>${t}</p></section>`).join("")}<div class="article-next"><span class="eyebrow">NEXT PERSPECTIVE</span><a href="${next.slug}.html">${next.title}<span aria-hidden="true">↗</span></a></div></div></div></article>${cta()}`,
    };
  });
}
